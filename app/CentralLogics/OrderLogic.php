<?php

namespace App\CentralLogics;

use App\CentralLogics\Helpers;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class OrderLogic {
    public static function track_order($order_id)
    {
        $order = Order::with(['details', 'delivery_address', 'delivery_man.rating', 'branch', 'offline_payment', 'prediction_duration_time_order'])
        ->where('id',  $order_id)->first();

        $order_details = OrderDetail::where('order_id', $order->id)->first();

        $product_id = $order_details?->product_id;
        $order['is_product_available'] = $product_id ? Product::find($product_id) ? 1 : 0 : 0;
        $order->offline_payment_information = $order->offline_payment ? json_decode($order->offline_payment->payment_info, true) : null;

        return Helpers::order_data_formatting($order, false);
    }
}