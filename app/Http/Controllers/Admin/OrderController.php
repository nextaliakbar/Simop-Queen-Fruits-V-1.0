<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\OfflinePayment;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private Order $order,
        private User $user,
        private DeliveryMan $delivery_man,
        private CustomerAddress $customer_address
    ) {}

    public function details($id): Renderable|RedirectResponse
    {
        $order = $this->order->with(['details', 'customer', 'delivery_address', 'branch', 'delivery_man'])
        ->where(['id' => $id])->first();

        if(!isset($order)) {
            Toastr::info('Tidak ada pesanan');
            return back();
        }

        $deliverymen = $this->delivery_man->where(['is_active' => 1])
        ->where(function($query) use ($order){
            $query->where('branch_id', $order->branch_id)
            ->orWhere('branch_id', 0);
        })->get();
        
        $delivery_date_time = $order['delivery_date'] . ' ' . $order['delivery_time'];
        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remaining_time;

        return view('admin-views.order.order-view', compact('order', 'deliverymen'));
    }

    public function generate_invoice($id): Renderable
    {
        $order = $this->order->where('id', $id)->first();

        return view('admin-views.order.invoice', compact('order'));
    }

    public function status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);

        if(in_array($order->order_status, ['delivered', 'failed'])) {
            Toastr::warning('Pesanan dengan status ' . $order->order_status . ' tidak dapat  diubah');
            return back();
        }

        if($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'], ['offline_payment', 'card'])) {
            Toastr::warning('Tambahkan referensi pembayaran terlebih dahulu');
            return back();
        }
        
        if($request->order_status == 'delivered' && $request->order_status == 'out_for_delivery' && $order['delivery_man_id'] == null && $order['order_type'] != 'take_away') {
            Toastr::warning('Tetapkan kurir terlebih dahulu');
            return back();
        }

        if($request->order_status == 'completed' && $order->payment_status != 'paid') {
            Toastr::warning('Perbarui status pembayaran terlebih dahulu');
            return back();
        }

        $order->order_status = $request->order_status;
        if($request->order_status == 'delivered') {
            $order->payment_status = 'paid';
        }

        $order->save();

        $message = Helpers::order_status_update_message($request->order_status);

        $store_name = Helpers::get_business_settings('store_name') ?? 'Queen Fruits';
        $delivery_man_name = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
        $customer_name = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '';

        $value = Helpers::text_variable_data_format(value: $message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $delivery_man_name, order_id: $order->id);

        $customer_fcm_token = $order->customer ? $order->customer->cm_firebase_token : null;

        try {
            if($value) {
                $data = [
                    'title' => 'Pesanan',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order_status'  
                ];

                if(isset($customer_fcm_token)) {
                    Helpers::send_push_notif_to_device(fcm_token:$customer_fcm_token, data:$data);
                }
            }
        } catch(\Exception $ex) {
            Toastr::warning('Push notification send failed for customer');
        }

        // Notifikasi kurir
        if($request->order_status == 'processing' || $request->order_status == 'out_for_delivery') {
            if(isset($order->delivery_man)) {
                $delivery_man_fcm_token = $order->delivery_man->fcm_token;
            }

            $value = 'Salah satu pesanan sedang diproses';
            $out_for_delivery = 'Salah satu pesanan sedang dalam proses pengiriman';

            try {
                if($value) {
                    $data = [
                        'title' => 'Pesanan',
                        'description' => $request->order_status == 'processing' ? $value : $out_for_delivery,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order_status'
                    ];

                    if(isset($delivery_man_fcm_token)) {
                        Helpers::send_push_notif_to_device(fcm_token: $delivery_man_fcm_token, data: $data);
                    }
                }
            } catch(\Exception $ex) {
                Toastr::warning('Push notification failed for delivery man');
            }
        }

        Toastr::success('Status pesanan diperbarui');
        return back();
    }

    public function add_payment_reference_code(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Kode referensi pembayaran ditambahkan');
        return back();
    }

    public function update_shipping(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required|min:11|max:13',
            'address' => 'required'
        ], [
            'contact_person_name.required' => 'Nama tidak boleh kosong',
            'address_type.required' => 'Jenis alamat tidak boleh kosong',
            'contact_person_number' => 'No. hp tidak boleh kosong',
            'address.required' => 'Alamat tidak boleh kosong'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        if($id) {
            $this->customer_address->where('id', $id)->update($address);
            Toastr::success('Alamat pengiriman diperarui');
        } else {
            $address = $this->customer_address;
            $address->contact_person_name = $request->input('contact_person_name');
            $address->contact_person_number = $request->input('contact_person_number');
            $address->address_type = $request->input('address_type');
            $address->latitude = $request->input('latitude');
            $address->longitude = $request->input('longitude');
            $address->user_id = $request->input('user_id');
            $address->address = $request->input('address');
            $address->save();

            $this->order->where('id', $request->input('order_id'))->update(['delivery_address_id' => $address->id]);
            Toastr::success('Alamat pengiriman ditambahkan');
        }

        return back();
    }

    public function preparation_time(Request $request, $id): RedirectResponse
    {
        $order = $this->order->with(['customer'])->find($id);
        $delivery_date_time = $order['delivery_date'] . ' ' . $order['delivery_time'];

        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'), strtotime($delivery_date_time));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');

        // Jika waktu pengiriman belum berakhir
        if(strtotime(date('Y-m-d H:i:s')) < strtotime($remaining_time)) {
            $delivery_date = new DateTime($remaining_time);
            $current_time = new DateTime();
            $interval = $delivery_date->diff($current_time);
            $remaining_minutes = $interval->i;
            $remaining_minutes += $interval->days * 24 * 60;
            $remaining_minutes += $interval->h * 60;
            $order->preparation_time = 0;
        } else {
            $delivery_time = new DateTime($remaining_time);
            $current_time = new DateTime();
            $interval = $delivery_time->diff($current_time);
            $diff_in_minutes = $interval->i;
            $diff_in_minutes += $interval->days * 24 * 60;
            $diff_in_minutes += $interval->h * 60;
            $order->preparation_time = 0; 
        }

        $new_delivery_date_time = Carbon::now()->addMinutes($request->extra_minute);
        $order->delivery_date = $new_delivery_date_time->format('Y-m-d');
        $order->delivery_time = $new_delivery_date_time->format('H:i:s');

        $order->save();

        $customer = $order->customer;

        $message = Helpers::order_status_update_message('customer_notify_message_for_time_change');
    
        $store_name = Helpers::get_business_settings('store_name') ?? 'Queen Fruits';
        $deliveryman_name = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
        $customer_name = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '';
        $value = Helpers::text_variable_data_format(value: $message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $deliveryman_name,order_id: $order->id);

        try {
            if($value) {
                $customer_fcm_token = null;
                $customer_fcm_token = $customer?->cm_firebase_token;

                $data = [
                    'title' => 'Pesanan',
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status'
                ];

                Helpers::send_push_notif_to_device(fcm_token:$customer_fcm_token, data: $data);
            } else {
                throw new \Exception('Gagal');
            }
        } catch(\Exception $ex) {
            Toastr::warning('Push notification send failed for customer');
        }

        Toastr::success('Waktu persiapan pesanan diperbarui');
        return back();
    }

    public function payment_status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);
        if($request->payment_status == 'paid' && $order['transaction_reference'] == null 
        && !in_array($order['payment_method'], ['offline_payment','card'])) {
            Toastr::warning('Tambahkan kode referensi pembayaran terlebih dahulu');
            return back();   
        }

        $order->payment_status = $request->payment_status;
        $order->save();

        Toastr::success('Status pembayaran diperbarui');
        return back();
    }

    public function add_deliveryman($order_id, $delivery_man_id): JsonResponse
    {
        if($delivery_man_id == 0) {
            return response()->json([], 200);
        }

        $order = $this->order->find($order_id);

        if($order->order_status == 'pending' || $order->order_status == 'delivered' 
        || $order->order_status == 'returned' || $order->order_status == 'failed' 
        || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }

        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $delivery_man_fcm_token = $order->delivery_man->fcm_token;
        $customer_fcm_token = null;

        if(isset($order->customer)) {
            $customer_fcm_token = $order->customer->cm_firebase_token;
        }

        $message = Helpers::order_status_update_message('del_assign');

        $store_name = Helpers::get_business_settings('store_name');
        $deliveryman_name = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
        $customer_name = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '';
        $value = Helpers::text_variable_data_format(value: $message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $deliveryman_name,order_id: $order->id);
    
        try {

            if($value) {
                $data = [
                    'title' => 'Pesanan',
                    'description' => $value,
                    'order_id' => $order_id,
                    'image' => '',
                    'type' => 'order_status'
                ];

                Helpers::send_push_notif_to_device(fcm_token: $delivery_man_fcm_token, data: $data, is_deliveryman_assigned: true);
            }

            // Kirim notifikasi ke pelanggan
            if(isset($order->customer) && $customer_fcm_token) {
                $notify_message = Helpers::order_status_update_message('customer_notify_message');
                $data['description'] = Helpers::text_variable_data_format(value: $notify_message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $deliveryman_name, order_id: $order->id);
                Helpers::send_push_notif_to_device(fcm_token: $customer_fcm_token, data: $data);
            }

        } catch(\Exception $ex) {
            Toastr::warning('Push notification failed for courier');
        }

        return response()->json(['status' => true], 200);
    }

    public function ajax_change_delivery_time_and_date(Request $request): JsonResponse
    {
        $order = $this->order->where('id', $request->order_id)->first();

        if(!$order) {
            return response()->json(['status' => false]);
        }

        $order->delivery_date = $request->input('delivery_date') ?? $order->delivery_date;
        $order->delivery_time = $request->input('delivery_time') ?? $order->delivery_time;
        $order->save();

        return response()->json(['status' => true]);
    }

    public function verify_offline_payment($order_id, $status): JsonResponse
    {
        $offline_data = OfflinePayment::where(['order_id' => $order_id])->first();

        if(!isset($offline_data)) {
            return response()->json(['status' => false], 200);
        }

        $offline_data->status = $status;
        $offline_data->save();

        $order = Order::find($order_id);

        if(!isset($order)) {
            return response()->json(['status' => false], 200);
        }

        if($offline_data->status == 1) {
            $order->order_status = 'confirmed';
            $order->payment_status = 'paid';
            $order->save();

            $message = Helpers::order_status_update_message('confirmed');

            $store_name = Helpers::get_business_settings('store_name') ?? 'Queen Fruits';
            $deliveryman_name = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
            $customer_name = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '';
            $value = Helpers::text_variable_data_format(value: $message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $deliveryman_name,order_id: $order->id);

            $customer_fcm_token = $order->customer ? $order->customer->cm_firebase_token : null;

            try {
                if($value && $customer_fcm_token != null) {
                    $data = [
                        'title' => 'Pesanan',
                        'description' => $value,
                        'order_id' => $order_id,
                        'image' => '',
                        'type' => 'order_status'
                    ];

                    Helpers::send_push_notif_to_device(fcm_token: $customer_fcm_token, data: $data);
                }
            } catch(\Exception $ex) {

            }
        } elseif($offline_data->status == 2) {
            $customer_fcm_token = $order->customer ? $order->customer->cm_firebase_token : null;

            if($customer_fcm_token != null) {
                try {
                    $data = [
                        'title' => 'Pesanan',
                        'description' => 'Pembayaran offline tidak terverifikasi',
                        'order_id' => $order_id,
                        'image' => '',
                        'type' => 'order_status'
                    ];
                    Helpers::send_push_notif_to_device(fcm_token: $customer_fcm_token, data: $data);
                } catch(\Exception $ex) {

                }
            }
        }
        return response()->json(['status' => true], 200);
    }

    public function list(Request $request, $status): Renderable
    {
        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $query_param = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $branch_id = $request['branch_id'];

        $query = $this->order->newQuery();

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });

            $query_param['search'] = $search;
        }

        if($branch_id && $branch_id != 0) {
            $query->where('branch_id', $branch_id);
            $query_param['branch_id'] = $branch_id;
        }

        if($from && $to) {
            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            $query_param['from'] = $from;
            $query_param['to'] = $to;
        }

        if($status == 'schedule') {
            $query->with(['customer', 'branch'])->schedule();
        } elseif($status != 'all') {
            $query->with(['customer', 'branch'])->where('order_status', $status)->notSchedule();
        } else {
            $query->with(['customer', 'branch']);
        }

        $key = explode(' ', $request['search']);

        $order_count = [
            'pending' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'pending'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'confirmed' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'confirmed'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),
            
            'processing' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'processing'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'out_for_delivery' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'out_for_delivery'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'delivered' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'delivered'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'canceled' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'canceled'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'returned' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'returned'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })
            ->when($request->has('search'), function($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })->count(),

            'failed' => $this->order
            ->notPos()
            ->notSchedule()
            ->where(['order_status' => 'failed'])
            ->when($branch_id && $branch_id != 0, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            })->count()
        ];

        $orders = $query->notPos()->latest()->paginate(Helpers::get_pagination())->appends($query_param);

        return view('admin-views.order.list', compact('orders', 'status', 'search', 'from', 'to', 'order_count', 'branch_id'));
    }
}
