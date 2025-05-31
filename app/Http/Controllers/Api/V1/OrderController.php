<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CustomerAddress;
use App\Models\DMReview;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductByBranch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(
        private Order $order,
        private User $user,
        private OrderDetail $order_detail,
        private Product $product,
        private ProductByBranch $product_by_branch,
        private OfflinePayment $offline_payment,
    ) {}

    public function place_order(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'payment_method' => 'required',
            'order_type' => 'required',
            'delivery_address_id' => 'required',
            'branch_id' => 'required',
            'delivery_time' => 'required',
            'delivery_date' => 'required',
            'distance' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        if(count($request['cart']) < 1) {
            return response()->json(['errors' => [['code' => 'Keranjang kosong', 'message' => 'keranjang kosong']]], 403);
        }

        $preparation_time = Branch::where(['id' => $request['branch_id']])->first()->preparation_time ?? 0;

        if($request['delivery_time'] == 'now') {
            $delivery_date = Carbon::now()->format('Y-m-d');
            $delivery_time = Carbon::now()->add($preparation_time, 'minute')->format('H:i:s');
        } else {
            $delivery_date = $request['delivery_date'];
            $delivery_time = $request['delivery_time'];
        }

        $user_id = auth('api')->user()->id;
        
        $payment_status = $request['payment_method'] == 'offline_payment' ? 'unpaid' : 'paid';

        $order_status = $request['payment_method'] == 'offline_payment' ? 'pending' : 'confirmed';
    
        if($request['order_type'] == 'take_away') {
            $delivery_charge = 0;
        } else {
            $delivery_charge = Helpers::get_delivery_charge($request['branch_id'], $request['distance']);
        }

        try {
            $order_id = 100000 + $this->order->all()->count() + 1;
            $order = [
                'id' => $order_id,
                'user_id' => $user_id,
                'order_amount' => $request['order_amount'],
                'payment_status' => $payment_status,
                'order_status' => $order_status,
                'payment_method' => $request['payment_method'],
                'transaction_reference' => $request['transaction_reference'] ?? null,
                'order_note' => $request['order_note'] ?? null,
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'delivery_address_id' => $request['delivery_address_id'],
                'delivery_date' => $delivery_date,
                'delivery_time' => $delivery_time,
                'delivery_address' => json_encode(CustomerAddress::find($request->delivery_address_id) ?? null),
                'delivery_charge' => $delivery_charge,
                'preparation_time' => $preparation_time,
                'created_at' => now(),
                'updated_at' => now(),
            ];
             $total_tax_amount = 0;

            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);

                $branch_product = $this->product_by_branch->where(['product_id' => $c['product_id'], 'branch_id' => $request['branch_id']])->first();

                //fixed stock quantity validation
                if($branch_product->stock_type == 'fixed' ){
                    $available_stock = $branch_product->stock - $branch_product->sold_quantity;
                    if ($available_stock < $c['quantity']){
                        return response()->json(['errors' => [['code' => 'stock', 'message' => 'Kuntitas melebihi stok yang tersedia']]], 403);
                    }
                }

                $discount_data = [];

                if ($branch_product) {
                    $branch_product_variations = $branch_product->variations;
                    $variations = [];
                    if (count($branch_product_variations)) {
                        $variation_data = Helpers::get_varient($branch_product_variations, $c['variations']);
                        $price = $branch_product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $branch_product['price'];
                    }
                    $discount_data = [
                        'discount_type' => $branch_product['discount_type'],
                        'discount' => $branch_product['discount'],
                    ];
                } else {
                    $product_variations = json_decode($product->variations, true);
                    $variations = [];
                    if (count($product_variations)) {
                        $variation_data = Helpers::get_varient($product_variations, $c['variations']);
                        $price = $product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
                    }
                    $discount_data = [
                        'discount_type' => $product['discount_type'],
                        'discount' => $product['discount'],
                    ];
                }

                $discount_on_product = Helpers::discount_calculate($discount_data, $price);

                $order_detail = [
                    'order_id' => $order_id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => $discount_on_product,
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    'variations' => json_encode($variations),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $total_tax_amount += $order_detail['tax_amount'] * $c['quantity'];
                $this->order_detail->insert($order_detail);

                $this->product->find($c['product_id'])->increment('popularity_count');

                //fixed stock quantity update
                if($branch_product->stock_type == 'fixed' ){
                    $branch_product->sold_quantity += $c['quantity'];
                    $branch_product->save();
                }
            }

            $order['total_tax_amount'] = $total_tax_amount;

            $this->order->insertGetId($order);


            if ($request->payment_method == 'offline_payment') {
                $offline_payment = $this->offline_payment;
                $offline_payment->order_id = $order['id'];
                $offline_payment->payment_info = json_encode($request['payment_info']);
                $offline_payment->save();
            }

            // $fcmToken = auth('api')->user()->cm_firebase_token;
            // $customerName = auth('api')->user()->f_name . ' '. auth('api')->user()->l_name;

            // $message = Helpers::order_status_update_message($order['order_status']);

            // $store_name = Helpers::get_business_settings('store_name');
            // $value = Helpers::text_variable_data_format(value:$message, user_name: $customerName, store_name: $store_name,  order_id: $order_id);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'order_id' => $order_id
            ], 200);
        } catch(\Exception $ex) {
            return response()->json([ $ex->getMessage()], 403);
        }
    }

    public function get_order_list(Request $request): JsonResponse
    {
        $user_id = auth('api')->user()->id;
        $order_filter = $request->order_filter;

        $orders = $this->order->with(['customer', 'delivery_man.rating', 'prediction_duration_time_order'])
        ->withCount('details')
        ->withCount(['details as total_quantity' => function($query) {
            $query->select(DB::raw('sum(quantity)'));
        }])
        ->where('user_id', $user_id)
        ->when($order_filter == 'past_order', function($query) use ($order_filter) {
            $query->whereIn('order_status', ['delivered', 'canceled', 'failed', 'returned']);
        })
        ->when($order_filter == 'running_order', function($query) use ($order_filter) {
            $query->whereNotIn('order_status', ['delivered', 'canceled', 'failed', 'returned']);
        })
        ->orderBy('id', 'DESC')
        ->paginate($request['limit'] , ['*'], 'page', $request['offset']);
    
        $orders->map(function($data) {
            $data['deliveryman_review_count'] = DMReview::where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();

            $order_id = $data->id;
            $order_details = $this->order_detail->where('order_id', $order_id)->first();
            $product_id = $order_details?->product_id;

            $data['is_product_available'] = $product_id ? $this->product->find($product_id) ? 1 : 0 : 0;
            $data['details_count'] = (int) $data->details_count;

            $product_images = $this->order_detail->where('order_id', $order_id)->pluck('product_id')
            ->filter()->map(function($product_id) {
                $product = $this->product->find($product_id);
                return $product ? $product->image : null;
            })->filter();

            $data['product_images'] = $product_images->toArray();

            return $data;
        });

        $order_array = [
            'total_size' => $orders->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders->items(),
        ];

        return response()->json($order_array, 200);
    }

    public function track_order_without_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = auth('api')->user()->id;

        $order = $this->order->where(['id' => $request['order_id'], 'user_id' => $user_id]);

        if(!isset($order)) {
            return response()->json([
               'errors' => [
                'code' => 'order',
                'message' => 'Pesanan tidak ditemukan'
               ] 
            ], 404);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    public function track_order_with_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order_id = $request['order_id'];
        $phone = $request['phone'];

        $order = $this->order->wit(['customer', 'delivery_address'])
        ->where('id', $order_id)
        ->where(function($query) use ($phone) {
            $query->where(function($sub_query) use ($phone) {
                $sub_query->whereHas('customer', function($customer_sub_query) use ($phone) {
                    $customer_sub_query->where('phone', $phone);
                });
            });
        })
        ->orWhere(function($sub_query) use ($phone) {
            $sub_query->whereHas('delivery_address', function($address_sub_query) use ($phone) {
                $address_sub_query->where('contact_person_number', $phone);
            });
        })->first();

        if(!isset($order)) {
            return response()->json([
               'errors' => [
                'code' => 'order',
                'message' => 'Pesanan tidak ditemukan'
               ] 
            ]);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    public function order_details_without_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = auth('api')->user()->id;

        $details = $this->order_detail->with(['order',
            'order.delivery_man' => function ($query) {
                $query->select('id', 'f_name', 'l_name', 'phone', 'email', 'image', 'branch_id', 'is_active');
            },
            'order.delivery_man.rating', 'order.delivery_address', 'order.offline_payment', 'order.deliveryman_review',
            'order.prediction_duration_time_order'])
            ->withCount(['reviews'])
            ->where(['order_id' => $request['order_id']])
            ->whereHas('order', function ($q) use ($user_id){
                $q->where('user_id', $user_id);
            })
            ->get();

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'Pesanan tidak ditemukan']
                ]
            ], 404);
        }

        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    public function order_details_with_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request['phone'];

        $details = $this->order_detail->with(['order', 'order.customer', 'order.delivery_address'])
            ->withCount(['reviews'])
            ->where(['order_id' => $request['order_id']])
            ->where(function ($query) use ($phone) {
                $query->where(function ($subQuery) use ($phone) {
                    $subQuery->whereHas('order', function ($orderSubQuery) use ($phone){
                        $orderSubQuery->whereHas('customer', function ($customerSubQuery) use ($phone) {
                                $customerSubQuery->where('phone', $phone);
                            });
                    });
                })
                    ->orWhere(function ($subQuery) use ($phone) {
                        $subQuery->whereHas('order', function ($orderSubQuery) use ($phone){
                            $orderSubQuery->whereHas('delivery_address', function ($addressSubQuery) use ($phone) {
                                    $addressSubQuery->where('contact_person_number', $phone);
                                });
                        });

                    });
            })
            ->get();

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'Pesanan tidak ditemukan']
                ]
            ], 404);
        }

        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    public function expenses_chart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
            'year' => 'required',
            'month' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $user_id = auth('api')->user()->id ?? -1;
            $branch_id = $request['branch_id'];
            $month = $request['month'];
            $year = $request['year'];

            $expenses = Order::get_expenses_chart($user_id, $branch_id, $month, $year);

            $is_february = $month == 2;

            $max_periods = $is_february ? 4 : 3;

            $chart_data = array_fill(0, $max_periods, 0);

            $period_labels = [];

            foreach($expenses as $expense) {
                $chart_data[$expense->period_number - 1] = (float) $expense->total_amount;
            }

            if($is_february) {
                $period_labels = [
                    'Periode 1 (1-7)',
                    'Periode 2 (8-14)',
                    'Periode 3 (15-21)',
                    'Periode 4 (22-28/29)'
                ];
            } else {
                $period_labels = [
                    'Periode 1 (1-10)',
                    'Periode 2 (11-20)',
                    'Periode 3 (21-31)'
                ];
            }

            $total_amount = array_sum($chart_data);
            return response()->json([
                'chart_data' => $chart_data,
                'period_labels' => $period_labels,
                'month' => $month,
                'year' => $year,
                'total_amount' => (float) $total_amount,
            ], 200);

        } catch(\Exception $ex) {
            return response()->json(['errors' => [['code' => 'server_error', 'message' => $ex->getMessage()]]], 500);

        }        
    }
}
