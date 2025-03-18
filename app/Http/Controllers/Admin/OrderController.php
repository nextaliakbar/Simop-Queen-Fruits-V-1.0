<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private Order $order,
        private User $user,
        private DeliveryMan $delivery_man
    ) {}

    public function details($id): Renderable|RedirectResponse
    {
        $order = $this->order->with(['details', 'customer', 'delivery_address', 'branch', 'delivery_man', 'order_partial_payments'])
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
        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'), strtotime($delivery_date_time));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remaining_time;

        return view('admin-views.order.order-view', compact('order', 'deliverymen'));
    }
}
