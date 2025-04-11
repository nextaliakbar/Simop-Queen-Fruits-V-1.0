<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflinePaymentMethodController extends Controller
{
    public function __construct(
        private Order $order
    ){}

    public function offline_payment_list(Request $request, $status)
    {
        $search = $request['search'];
        $status_mapping = [
            'pending' => 0,
            'denied' => 2
        ];

        $status = $status_mapping[$status];

        $orders = $this->order->with(['offline_payment'])
        ->where(['payment_method' => 'offline_payment', 'branch_id' => auth('branch')->id()])
        ->whereHas('offline_payment', function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($request->has('search'), function ($query) use ($request) {
            $keys = explode(' ', $request['search']);
            return $query->where(function ($query) use ($keys) {
                foreach($keys as $key) {
                    $query->where('id', 'like', "%{$key}%")
                    ->orWhere('order_status', 'like', "%{$key}%")
                    ->orWhere('payment_status', 'like', "%{$key}%");
                }
            });
        })->latest()->paginate(Helpers::get_pagination());

        return view('branch-views.order.offline-payment.list', compact('orders', 'search'));
    }

    public function quick_view_details(Request $request): JsonResponse
    {
        $order = $this->order->find($request->id);

        return response()->json([
            'view' => view('branch-views.order.offline-payment.details-quick-view', compact('order'))
        ]);
    }
}
