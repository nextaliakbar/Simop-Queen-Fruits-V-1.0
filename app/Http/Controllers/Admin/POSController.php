<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\DeliveryMan;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductByBranch;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class POSController extends Controller
{
    public function __construct(
        private User $user,
        private Admin $admin,
        private Branch $branch,
        private Category $category,
        private Product $product,
        private Order $order,
        private ProductByBranch $product_by_branch,
        private OrderDetail $order_detail,
        private DeliveryMan $delivery_man
    ) {}

    public function index(Request $request)
    {
        $selected_branch = session()->get('selected_branch') ?? 1;
        session()->put('branch_id', $selected_branch);

        $category = $request->query('category_id', 0);
        $categories = $this->category->where(['position' => 0])->active()->get();
        $keyword = $request->keyword;
        $key = explode(' ', $keyword);

        $products = $this->product
        ->with(['branch_products' => function ($q) use ($selected_branch) {
            $q->where(['is_available' => 1, 'branch_id' => $selected_branch]);
        }])
        ->whereHas('branch_products', function ($q) use ($selected_branch) {
            $q->where(['is_available' => 1, 'branch_id' => $selected_branch]);
        })
        ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
            $query->whereJsonContains('category_ids', [['id' => (string) $request['category_id']]]);
        })
        ->when($keyword, function($query) use ($key) {
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->active()->latest()->paginate(Helpers::get_pagination());

        $branches = $this->branch->select('id', 'name')->get();
        
        return view('admin-views.pos.index', compact('categories', 'products', 'category', 'keyword', 'branches'));
    }

    public function order(Request $request): RedirectResponse
    {
        if($request->session()->has('cart')) {
            if(count($request->session()->get('cart')) < 1) {
                Toastr::error('Peringatan keranjang kosong');
                return back();
            }
        } else {
            Toastr::error('Peringatan keranjang kosong');
            return back();
        }

        $order_type = session()->has('order_type') ? session()->get('order_type') : 'take_away';

        $delivery_charge = 0;

        if($order_type == 'home_delivery') {
            if(!session()->has('customer_id')) {
                Toastr::error('Pilih pelanggan terlebih dahulu');
                return back();
            }

            if(!session()->has('address')) {
                Toastr::error('Alamat pengiriman belum diisi');
                return back();
            }

            $address_data = session()->get('address');
            $distance = $address_data['distance'] ?? 0;
            
            $delivery_charge = Helpers::get_delivery_charge(session()->get('branch_id') ?? 1, $distance);

            $address = [
                'address_type' => 'Home',
                'contact_person_name' => $address_data['contact_person_name'],
                'contact_person_number' => $address_data['contact_person_number'],
                'address' => $address_data['address'],
                'longitude' => (string) $address_data['longitude'],
                'latitude' => (string) $address_data['latitude'],
                'user_id' => session()->get('customer_id')
            ];

            $customer_address = CustomerAddress::create($address);
        }

        $cart = $request->session()->get('cart');
        $total_tax_amount = 0;
        $product_price = 0;
        $order_details = [];

        $order_id = 100000 + $this->order->all()->count() + 1;
        
        if($this->order->find($order_id)) {
            $order_id = $this->order->orderBy('id', 'DESC')->first()->id + 1;
        }

        $order = $this->order;
        $order->id = $order_id;

        $order->user_id = session()->get('customer_id') ?? null;
        $order->payment_status = $order_type == 'take_away' ? 'paid' : 'unpaid';
        $order->order_status = $order_type == 'take_away' ? 'delivered' : 'confirmed';
        $order->order_type = $order_type == 'take_away' ? 'pos' : 'delivery';
        $order->payment_method = $request->type;
        $order->transaction_reference = $request->transaction_reference ?? null;
        $order->delivery_charge = $delivery_charge;
        $order->delivery_address_id = $order_type == 'home_delivery' ? $customer_address->id : null;
        $order->delivery_date = Carbon::now()->format('Y-m-d');
        $order->delivery_time = Carbon::now()->format('H:i:s');
        $order->order_note = null;
        $order->checked = 1;

        $total_product_main_price = 0;

        // Cek jika diskon lebih besar dari pada total harga
        $total_price_for_discount_validation = 0;

        foreach($cart as $c) {
            if(is_array($c)) {
                $discount_on_product = 0;
                $discount = 0;
                $product_subtotal = ($c['price'] * $c['quantity']);
                $discount_on_product += ($c['discount'] * $c['quantity']);

                $total_price_for_discount_validation += $c['price'];

                $product = $this->product->find($c['id']);
                if($product) {
                    $price = $c['price'];

                    $product = Helpers::product_data_formatting($product);

                    $branch_product = $this->product_by_branch->where(['product_id' => $c['id'], 'branch_id' => session()->get('branch_id')])->first();

                    $discount_data = [];

                    if(isset($branch_product)) {
                        $variation_data = Helpers::get_varient($branch_product->variations, $c['variations']);

                        $discount_data = [
                            'discount_type' => $branch_product['discount_type'],
                            'discount' => $branch_product['discount']
                        ];
                    }

                    $discount = Helpers::discount_calculate($discount_data, $price);
                    $variations = $variation_data['variations'];

                    $order_d = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => Helpers::tax_calculate($product, $price),
                        'discount_on_product' => $discount,
                        'discount_type' => 'discount_on_product',
                        'variations' => json_encode($variations)
                    ];

                    $total_tax_amount += $order_d['tax_amount'] * $c['quantity'];
                    $product_price += $product_subtotal - $discount_on_product;
                    $total_product_main_price += $product_subtotal;
                    $order_details[] = $order_d;
                }
            }
        }

        $total_price = $product_price;
        if(isset($cart['extra_discount'])) {
            $extra_discount = $cart['extra_discount_type'] == 'percent' && $cart['extra_discount'] > 0 ? (($total_product_main_price * $cart['extra_discount'])/100) : $cart['extra_discount'];
            $total_price -= $extra_discount;
        }

        if(isset($cart['extra_discount']) && $cart['extra_discount_type'] == 'amount') {
            if($cart['extra_discount'] > $total_price_for_discount_validation) {
                Toastr::error('Jumlah diskon tidak boleh melebihi dari total harga produk');
                return back();
            }
        }

        $tax = isset($cart['tax']) ? $cart['tax'] : 0;
        $total_tax_amount = ($tax > 0) ? (($total_price * $tax) / 100) : $total_tax_amount;

        try {

            $order->extra_discount = $extra_discount ?? 0;
            $order->total_tax_amount = $total_tax_amount;
            $order->order_amount = $total_price + $total_tax_amount;
            $order->coupon_discount_amount = 0.00;
            $order->branch_id = session()->get('branch_id');

            if(session('branch_id')) {
                $order->save();

                foreach($order_details as $key => $item) {
                    $order_details[$key]['order_id'] = $order->id;
                }

                $this->order_detail->insert($order_details);

                session()->forget('cart');
                session(['last_order' => $order->id]);
                session()->forget('customer_id');
                session()->forget('branch_id'); 
                session()->forget('address'); 
                session()->forget('order_type');
                
                Toastr::success('Pesanan berhasil dibuat');
                
                // Kirim notifikasi ke pelanggan untuk pesanan tipe pengiriman
                
                if($order->order_type == 'delivery') {
                    $message = Helpers::order_status_update_message('confirmed');
                    $customer = $this->user->find($order->user_id);
                    $customer_fcm_token = $customer?->cm_firebase_token;
                    $customer_name = $customer?->f_name . ' ' . $customer?->l_name ?? '';

                    $store_name = 'Queen Fruits';
                    $value = Helpers::text_variable_data_format(value: $message, user_name: $customer_name, store_name: $store_name, order_id : $order_id);
                
                    if($value && isset($customer_fcm_token)) {
                        $data = [
                            'title' => 'Pesanan',
                            'description' => $value,
                            'order_id' => $order_id,
                            'image' => '',
                            'type' => 'order_status'
                        ];

                        Helpers::send_push_notif_to_device($customer_fcm_token, $data);
                    }
                }

                // Kirim email
                try {
                    $email_service = []; // mail_config
                    $order_mail_status = 0; //place_order_mail_status_user
                    if(isset($email_service['status']) && $email_service['status'] == 1 && $order_mail_status == 1 && isset($customer)) {
                        
                    }
                } catch(\Exception $ex) {
                    info($ex);
                }
                
                return back();
            } else {
                Toastr::warning('Silahkan pilih salah satu cabang');
            }

        } catch(\Exception $ex) {
            info($ex);
        }
        return back();
    }

    public function customer_store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required'
        ], [
            'f_name.required' => 'Nama depan tidak boleh kosong',
            'l_name.required' => 'Nama belakang tidak boleh kosong',
            'phone.required' => 'No. Hp tidak boleh kosong'
        ]);

        $user_phone = $this->user->where('phone', $request->phone)->first();
        if(isset($user_phone)) {
            Toastr::error('No. Hp sudah tersedia');
            return back();
        }

        $this->user->create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'password' => bcrypt("{$request->f_name}{$request->l_name}")
        ]);

        Toastr::success('Pelanggan berhasil ditambahkan');
        return back();
    }

    public function quick_view(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->product_id);

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos._quick-view-data', compact('product'))->render()
        ]);
    }

    public function variant_price(Request $request): array
    {
        $product = $this->product->find($request->id);

        $price = $product->price;

        $branch_product = $this->product_by_branch->where(['product_id' => $request->id, 'branch_id' => session()->get('branch_id')])->first();

        if(isset($branch_product)) {
            $branch_product_variations = $branch_product->variations;

            $discount_data = [
                'discount_type' => $branch_product['discount_type'],
                'discount' => $branch_product['discount']
            ];

            if($request->variations && count($branch_product_variations)) {
                $price_total = $branch_product['price'] + Helpers::new_variation_price($branch_product_variations, $request->variations);
                $price = $price_total - Helpers::discount_calculate($discount_data, $price_total);   
            } else {
                $price = $branch_product['price'] - Helpers::discount_calculate($discount_data, $branch_product['price']);

            }
        }

        return [
            'price' => 'Rp ' . number_format($price * $request->quantity)
        ];
    }

    public function add_to_cart(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);

        $data = [];
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $variation_price = 0;

        $branch_product = $this->product_by_branch->where(['product_id' => $request->id, 'branch_id' => session()->get('branch_id')])->first();

        $branch_product_price = 0;
        $discount_data = [];

        if(isset($branch_product)) {
            $branch_product_variations = $branch_product->variations;

            if($request->variations && count($branch_product_variations)) {
                foreach($request->variations as $key => $value) {
                    if($value['required'] == 'on' && !isset($value['values'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => 'Silahkan pilih produk dari' . ' ' . $value['name']
                        ]);
                    }

                    if(isset($value['values'])  && $value['min'] != 0 && $value['min'] > count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => 'Silahkan pilih minimal ' . $value['min'] . ' Untuk ' . $value['name'] . '.'
                        ]);
                    }

                    if(isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => 'Silahkan pilih maksimal ' . $value['max'] . ' Untuk ' . $value['name'] . '.'
                        ]);
                    }
                }

                $variation_data = Helpers::get_varient($branch_product_variations, $request->variations);
                $variation_price = $variation_data['price'];
                $variations = $request->variations;
            }

            $branch_product_price = $branch_product['price'];
            $discount_data = [
                'discount_type' => $branch_product['discount_type'],
                'discount' => $branch_product['discount']
            ];
        }

        $price = $branch_product_price + $variation_price;
        $data['variation_price'] = $variation_price;

        $dsicount_on_product = Helpers::discount_calculate($discount_data, $price);

        $data['variations'] = $variations;
        $data['variant'] = $str;
        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = $dsicount_on_product;
        $data['image'] = $product->image;

        if($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return response()->json(['data' => $data]);
    }

    public function remove_from_cart(Request $request): JsonResponse
    {
        if($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return response()->json([], 200);
    }

    public function empty_cart(Request $request): JsonResponse
    {
        session()->forget('cart');
        Session::forget('customer_id');
        session()->forget('address');
        session()->forget('order_type');

        return response()->json([], 200);
    }

    public function cart_items(): Renderable
    {
        return view('admin-views.pos._cart');
    }

    public function update_quantity(Request $request): JsonResponse
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }

            return $object;
        });

        $request->session()->has('cart', $cart);
        return response()->json([], 200);
    }

    public function get_customers(Request $request): JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = $this->user->where(['user_type' => null])
        ->where(function ($q) use ($key) {
            foreach($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                ->orWhere('l_name', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
            }
        })->whereNotNull(['f_name', 'l_name', 'phone'])
        ->limit(8)
        ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone, ")") as text')]);

        return response()->json($data);
    }

    public function store_keys(Request $request): JsonResponse
    {
        session()->put($request['key'], $request['value']);
        return response()->json($request['key'], 200);
    }

    public function session_destroy(Request $request): JsonResponse
    {
        Session::forget('cart');
        Session::forget('customer_id');
        session()->forget('address');
        session()->forget('order_type');

        return response()->json([], 200);
    }

    public function order_type_store(Request $request): JsonResponse
    {
        session()->put('order_type', $request['order_type']);
        return response()->json($request['order_type'], 200);
    }

    public function add_delivery_info(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ], [
            'contact_person_name.required' => 'Nama pelanggan tidak boleh kosong',
            'contact_person_number.required' => 'No. Hp pelanggan tidak boleh kosong',
            'address.required' => 'Alamat tidak boleh kosong'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $branch_id = session()->get('branch_id');
        $branch = $this->branch->find($branch_id);
        $origin_lat = $branch['latitude'];
        $origin_lng = $branch['longitude'];
        $destination_lat = $branch['latitude'];
        $destination_lng = $branch['longitude'];

        if($request->has('latitude') && $request->has('longitude')) {
            $api_key  = ''; // map_api_key_server
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origin=' . $origin_lat . ',' . $origin_lng . '&destination=' . $destination_lat . ',' . $destination_lng . '&key=' . $api_key);

            $data = json_decode($response, true);
            $distance_value = ($data['rows'][0]['elements'][0]['distance']['value']) ?? 0;
            $distance = $distance_value / 1000;
        }

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'Home',
            'address' => $request->address,
            'distance' => $distance ?? 0,
            'longitude' => (string) $request->longitude ?? '-8.200786388659255',
            'latitude' => (string) $request->latitude ?? '113.53614807128906',
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('admin-views.pos._address', compact('address'))->render()
        ]);
    }

    public function update_discount(Request $request): RedirectResponse
    {
        if(session()->has('cart')) {
            if(count(session()->get('cart')) < 1) {
                Toastr::error('Keranjang masih kosong');
                return back();
            }
        } else {
            Toastr::error('Keranjang masih kosong');
            return back();
        }

        if($request->type == 'percent' && $request->discount < 0) {
            Toastr::error('Ekstra diskon tidak boleh kurang dari 0 %');
            return back();
        } elseif($request->type == 'percent' && $request->discount > 100) {
            Toastr::error('Ekstra diskon tidak boleh lebih dari 100 %');
            return back();
        }

        $total_price = 0;
        foreach(session()->get('cart') as $cart) {
            if(isset($cart['price'])) {
                $total_price += ($cart['price'] - $cart['discount']);
            }
        }

        if($request->type == 'amount' && $request->discount > $total_price) {
            Toastr::error('Ekstra diskon tidak boleh lebih dari total pesanan produk');
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['extra_discount_type'] = $request->type;
        $cart['extra_discount'] = $request->discount;

        $request->session()->put('cart', $cart);
        return back();
    }

    public function list(Request $request) : Renderable
    {
        $query_param = [];
        $search = $request['search'];
        $branch_id = $request['branch_id'];
        $from = $request['from'];
        $to = $request['to'];

        $query = $this->order->pos()->with(['customer', 'branch']);
        $branches = $this->branch->all();

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $request['search']];
        } elseif($request->has('filter')) {
            $query->when($from && $to && $branch_id == 'all', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($from && $to &&$branch_id != 'all', function($q) use ($from, $to, $branch_id) {
                $q->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
                ->whereHas('branch', function ($q) use ($branch_id) {
                    $q->where('id', $branch_id);
                });
            })
            ->when($from == null && $to == null && $branch_id != 'all', function ($q) use ($from, $to, $branch_id) {
                $q->whereHas('branch', function ($q) use ($branch_id) {
                    $q->where('id', $branch_id);
                });
            })->get();

            $query_param = [
                'filter' => '', 
                'branch_id' => $request['branch_id'] ?? '', 
                'from' => $request['from'] ?? '', 
                'to' => $request['to'] ?? ''
            ];
        }

        $orders = $query->latest()->paginate(Helpers::get_pagination())->appends($query_param);

        return view('admin-views.pos.order.list', compact('orders', 'search', 'branches', 'from', 'to', 'branch_id'));
    }

    public function order_details($id): Renderable|RedirectResponse
    {
        $order = $this->order->with('details')->where(['id' => $id])->first();

        if(!(isset($order))) {
            Toastr::info('Tidak ada pesanan / penjualan');
            return back();
        }

        $deliverymen = $this->delivery_man->where(['is_active' => 1])
        ->where(function ($query) use ($order) {
            $query->where('branch_id', $order->branch_id)
            ->orWhere('branch_id', 0);
        })->get();

        return view('admin-views.order.order-view', compact('order', 'deliverymen'));
    }

    public function generate_invoice($id): JsonResponse
    {
        $order = $this->order->where('id', $id)->first();

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos.order.invoice', compact('order'))->render()
        ]);
    }
}
