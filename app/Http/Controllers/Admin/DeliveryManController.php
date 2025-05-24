<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryMan;
use App\Models\DMReview;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryManController extends Controller
{
    public function __construct(
        private DeliveryMan $deliveryman,
        private Order $order,
        private Branch $branch,
        private DMReview $dm_review
    ) {}

    public function index(): Renderable
    {
        return view('admin-views.delivery-man.index');
    }
    
    public function list(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');
        $date_range = $request->input('date');
        $status = $request->input('status');

        if($date_range) {
            [$start_date, $end_date] = explode(' - ', $date_range);

            $start_date_time = Carbon::parse($start_date)->startOfDay();
            $end_date_time = Carbon::parse($end_date)->endOfDay();
        } else {
            $start_date_time = null;
            $end_date_time = null;
        }

        $query = $this->deliveryman->withCount([
            'orders',
            'orders as ongoing_orders_count' => function($query) {
                $query->whereIn('order_status', ['pending', 'confirmed', 'processing', 'out_for_delivery']);
            },
            'orders as canceled_orders_count' => function($query) {
                $query->where('order_status', 'canceled');
            },
            'orders as completed_orders_count' => function($query) {
                $query->where('order_status', 'delivered');
            }
        ])->when($search, function($query) use ($search, &$query_param){
            $key = explode(' ', $search);
            $query->where(function($q) use ($key){
                foreach($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
                }
            });

            $query_param['search'] = $search;
        })->when(($start_date_time != null && $end_date_time != null), function($query) use ($start_date_time, $end_date_time){
            $query->whereBetween('created_at', [$start_date_time, $end_date_time]);
        })->when(($request->has('status') && $status == 'active'), function($query) use ($status){
            $query->where(['is_active' => 1]);
        })->when(($request->has('status') && $status == 'inactive'), function($query) use ($status) {
            $query->where(['is_active' => 0]);
        })->where('application_status', 'approved');

        $active_count = (clone $query)->where('is_active', 1)->count();
        $in_active_count = (clone $query)->where('is_active', 0)->count();

        $deliverymen = $query->withSum('orders as order_amount', 'order_amount')
        ->withSum('orders as delivery_charge', 'delivery_charge')
        ->latest()->paginate(Helpers::get_pagination())
        ->appends($query_param);

        $deliverymen->getCollection()->transform(function ($deliveryman) {
           $deliveryman->total_order_amount = $deliveryman->order_amount +  $deliveryman->delivery_charge;
           return $deliveryman;
        });

        return view('admin-views.delivery-man.list', compact('deliverymen', 'search', 'status', 'active_count', 'in_active_count', 'start_date_time', 'end_date_time'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:delivery_men',
            'age' => 'required',
            'phone' => 'required|unique:delivery_men',
            'confirm_password' => 'same:password'
        ], [
            'f_name.required' => 'Nama tidak boleh kosong',
            'age.required' => 'Usia tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah tersedia',
            'phone.required' => 'No hp tidak boleh kosong',
            'phone.unique' => 'No hp sudah tersedia'
        ]);

        $identity_image_names = [];

        if(!empty($request->file('identity_image'))) {
            foreach($request->identity_image as $img) {
                $identity_image_names[] = Helpers::upload('delivery-man/', $request->file('identity_image')->getClientOriginalExtension(), $img);
            }

            $identity_image = json_encode($identity_image_names);
        } else {
            $identity_image = json_encode([]);
        }

        $deliveryman = $this->deliveryman;
        $deliveryman->f_name = $request->f_name;
        $deliveryman->l_name = $request->l_name;
        $deliveryman->age = $request->age;
        $deliveryman->email = $request->email;
        $deliveryman->phone = $request->phone;
        $deliveryman->identity_number = $request->identity_number;
        $deliveryman->identity_type = $request->identity_type;
        $deliveryman->branch_id = $request->branch_id;
        $deliveryman->identity_image = $identity_image;
        $deliveryman->image = Helpers::upload('delivery-man/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
        $deliveryman->password = bcrypt($request->password);
        $deliveryman->application_status = 'approved';
        $deliveryman->save();

        Toastr::success('Kurir berhasil ditambahkan');
        return redirect('admin/delivery-man/list');
    }

    public function ajax_is_active(Request $request): JsonResponse
    {
        $deliveryman = $this->deliveryman->find($request->id);
        $deliveryman->is_active = $request->status;
        $deliveryman->save();

        return response()->json([
            'status' => $deliveryman->is_active,
            'message' => 'Status berhasil diubah'
        ]);
    }

    public function details(Request $request, $id)
    {
        $deliveryman = $this->deliveryman->find($id);
        $branches = $this->branch->get();

        $branch_id = $request->input('branch_id');
        $search = $request->input('search');
        $date_range = $request->input('date');

        if($date_range) {
            [$start_date, $end_date] = explode(' - ', $date_range);

            $start_date_time = Carbon::parse($start_date)->startOfDay();
            $end_date_time = Carbon::parse($end_date)->endOfDay();
        } else {
            $start_date_time = null;
            $end_date_time = null;
        }

        $orders_query = $this->order->where('delivery_man_id', $id)
        ->withCount('details')
        ->when($search, function($query) use($search) {
            $key = explode(' ', $search);
            $query->where(function($q) use($key){
                foreach($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            });
        })->when(($start_date_time != null && $end_date_time != null), function($query) use ($start_date_time, $end_date_time){
            $query->whereBetween('created_at', [$start_date_time, $end_date_time]);
        })->when($branch_id, function($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        });

        $orders = (clone $orders_query)->latest()->paginate(Helpers::get_pagination());

        $order_amount = (clone $orders_query)->sum('order_amount');
        $delivery_charge = (clone $orders_query)->sum('delivery_charge');
        $total_order_amount = $order_amount + $delivery_charge;
    
        $pending_orders = (clone $orders_query)->whereIn('order_status', ['pending', 'confirmed', 'processing'])->count();
        $out_for_delivery_orders = (clone $orders_query)->where('order_status', 'out_for_delivery')->count();
        $completed_orders = (clone $orders_query)->where('order_status', 'delivered')->count();

        return view('admin-views.delivery-man.details', compact('deliveryman', 'branches', 'orders', 'total_order_amount', 'pending_orders', 'out_for_delivery_orders', 'completed_orders', 'branch_id', 'search', 'start_date_time', 'end_date_time'));
    }

    public function edit($id): Renderable
    {
        $deliveryman = $this->deliveryman->find($id);
        return view('admin-views.delivery-man.edit', compact('deliveryman'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'age' => 'required',
            'phone' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
        ], [
            'f_name.required' => 'Nama tidak boleh kosong',
            'age.required' => 'Usia tidak boleh kosong',
            'phone.required' => 'No hp tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
        ]);

        if($request->password) {
            $request->validate([
                'confirm_password' => 'same:password'
            ], [
                'confirm_password.same' => 'Konfirmasi password harus sama dengan password' 
            ]);
        }

        $deliveryman = $this->deliveryman->find($id);

        if($deliveryman['email'] != $request['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men'
            ], [
                'email.required' => 'Email tidak boleh kosong',
                'email.unique' => 'Email sudah tersedia'
            ]);
        }

        if($deliveryman['phone'] != $request['phone']) {
            $request->validate([
                'phone' => 'required|unique:delivery_men'
            ], [
                'phone.required' => 'No hp tidak boleh kosong',
                'phone.unique' => 'No hp sudah tersedia'
            ]);
        }

        if(!empty($request->file('identity_image'))) {
            foreach(json_decode($deliveryman['identity_image'], true) as $img) {
                if(Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }

            $img_keeper = [];
            foreach($request->identity_image as $img) {
                $img_keeper[] = Helpers::upload('delivery-man/', $img->getClientOriginalExtension(), $img);
            }
            
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $deliveryman['identity_image'];
        }

        $deliveryman->f_name = $request->f_name;
        $deliveryman->l_name = $request->l_name;
        $deliveryman->age = $request->age;
        $deliveryman->email = $request->email;
        $deliveryman->phone = $request->phone;
        $deliveryman->identity_number = $request->identity_number;
        $deliveryman->identity_type = $request->identity_type;
        $deliveryman->branch_id = $request->branch_id;
        $deliveryman->identity_image = $identity_image;
        $deliveryman->image = $request->has('image') ? Helpers::update('delivery-man/', $deliveryman->image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $deliveryman->image;
        $deliveryman->password = strlen($request->passworrd) > 1 ? bcrypt($request->password) : $deliveryman['password'];
        $deliveryman->save();
    
        Toastr::success('Kurir berhasil diperbarui');
        return redirect('admin/delivery-man/list');
    }

    public function delete(Request $request): RedirectResponse
    {
        $deliveryman = $this->deliveryman->find($request->id);

        if(Storage::disk('public')->exists('delivery-man/' . $deliveryman['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $deliveryman['image']);
        }

        foreach(json_decode($deliveryman['identity_image'], true) as $img) {
            if(Storage::disk('public')->exists('delivery-man/' . $img)) {
                Storage::disk('public')->delete('delivery-man/' . $img);
            }
        }

        $deliveryman->delete();

        Toastr::success('Kurir berhasil dihapus');
        return back();
    }

    public function review_list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        
        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $deliveryman_ids = $this->deliveryman->where(function($q) use($key){
                foreach($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();

            $reviews = $this->dm_review->whereIn('delivery_man_id', $deliveryman_ids);
            $query_param = ['search' => $search['search']];
        } else {
            $reviews = $this->dm_review;
        }

        $reviews = $reviews->with(['delivery_man', 'customer'])->latest()->paginate(Helpers::get_pagination())->appends($query_param);
        return view('admin-views.delivery-man.reviews-list', compact('reviews', 'search'));
    }

    public function preview($id): Renderable
    {
        $deliveryman = $this->deliveryman->with(['reviews'])->where(['id' => $id])->first();
        $reviews = $this->dm_review->where(['delivery_man_id' => $id])->latest()->paginate(Helpers::get_pagination());
        return view('admin-views.delivery-man.view', compact('deliveryman', 'reviews'));
    }
}
