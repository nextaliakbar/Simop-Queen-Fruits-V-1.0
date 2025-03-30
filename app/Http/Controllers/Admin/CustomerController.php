<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function __construct(
        private User $customer,
        private Order $order
    ) {}

    public function list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = $this->customer->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $request['search']];
        } else {
            $customers = $this->customer;
        }

        $customers = $customers->with(['orders'])->where('user_type', null)->latest()->paginate(Helpers::get_pagination())
        ->appends($query_param);
        return view('admin-views.customer.list', compact('customers', 'search'));
    }

    public function view($id, Request $request): RedirectResponse|Renderable
    {
        $search = $request->search;
        $customer = $this->customer->find($id);

        if(!isset($customer)) {
            Toastr::error('Pelanggan tidak ada');
            return back();
        }

        $orders = $this->order->latest()->where(['user_id' => $id])
        ->when($search, function($query) use ($search) {
            $key = explode(' ', $search);
            foreach($key as $value) {
                $query->where('id', 'like', "%{$value}%");
            }
        })->paginate(Helpers::get_pagination())
        ->appends(['search' => $search]);

        return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
    }

    public function update_status(Request $request, $id): JsonResponse
    {
        $this->customer->findOrFail($id)->update(['is_active' => $request['status']]);
        return response()->json($request['status']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            $this->customer->findOrFail($request['id'])->delete();
            Toastr::success('Pelanggan berhasil dihapus');
        } catch(\Exception $ex) {   
            Toastr::error('Pelanggan tidak ditemukan');
        }

        return back();
    }
}
