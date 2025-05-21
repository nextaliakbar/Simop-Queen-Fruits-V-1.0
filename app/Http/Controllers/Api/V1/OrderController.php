<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CustomerAddress;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductByBranch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
                'user_id' => '15',
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
}
