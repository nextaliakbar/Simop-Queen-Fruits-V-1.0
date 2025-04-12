<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function __construct(
        private User $customer,
        private Order $order
    ){}

    public function view($id, Request $request): RedirectResponse|Renderable
    {
        $search = $request->search;
        $customer = $this->customer->find($id);

        if(!isset($customer)) {
            Toastr::error('Pelanggan tidak ada');
            return back();
        }

        $orders = $this->order->latest()->where(['user_id' => $id, 'branch_id' => auth('branch')->id()])
        ->when($search, function($query) use ($search) {
            $key = explode(' ', $search);
            foreach($key as $value) {
                $query->where('id', 'like', "%{$value}%");
            }
        })->paginate(Helpers::get_pagination())
        ->appends(['search' => $search]);

        return view('branch-views.customer.customer-view', compact('customer', 'orders', 'search'));
    }
}
