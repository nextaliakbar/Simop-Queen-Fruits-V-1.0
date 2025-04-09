<?php

namespace App\CentralLogics;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\DMReview;
use App\Models\Review;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function error_processor($validator) {
        $error_keeper = [];
        foreach($validator->errors()->getMessages() as $index => $error) {
            $error_keeper[] = ['code' => $index, 'message' => $error[0]];
        }

        return $error_keeper;
    }

    public static function get_business_settings(string $name)
    {
        $config = null;

        $settings = Cache::rememberForever(CACHE_BUSINESS_SETTINGS_TABLE, function(){
            return BusinessSetting::all();
        });

        $data = $settings?->firstWhere('key', $name);

        if(isset($data)) {
            $config = json_decode($data['value'], true);

            if(is_null($config)) {
                $config = $data['value'];
            }
        }

        return $config;
    }

    public static function on_error_image($data, $src, $error_src, $path)
    {
        if(isset($data) && strlen($data) > 1 &&  Storage::disk('public')->exists($path.$data)) {
            return $src;
        }

        return $error_src;
    }

    public static function module_permission_check($mod_name)
    {
        $permission = auth('admin')->user()->role->module_access??null;

        if(isset($permission) && in_array($mod_name, (array)json_decode($permission))) {
            return true;
        }

        if(auth('admin')->user()->admin_role_id == 1) {
            return true;
        }

        return false;
    }

    public static function upload(string $dir, string $format, $image = null)
    {

        if($image != null) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if(!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $image_name, file_get_contents($image));
        } else {
            $image_name = 'def.png';
        }
        return $image_name;
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if(Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $image_name = Helpers::upload($dir, $format, $image);
        return $image_name;
    }

    public static function delete($full_path_image, $full_path_banner = null)
    {
        if(Storage::disk('public')->exists($full_path_image)) {
            Storage::disk('public')->delete($full_path_image);
        }

        if(!is_null($full_path_banner)) {
            if(Storage::disk('public')->exists($full_path_banner)) {
                Storage::disk('public')->delete($full_path_banner);
            }
        }

        return [
            'success' => 1,
            'message' => 'berhasil dihapus'
        ];
    }

    public static function get_pagination()
    {
        $pagination_limit = Helpers::get_business_settings('pagination_limit');
        return $pagination_limit ?? 25;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        $price_tax = 0;

        if(!is_null($product['tax_type'])) {
            if($product['tax_type'] == 'percent') {
                $price_tax = ($price / 100) * $product['tax'];
            } else {
                $price_tax = $product['tax'];
            }
        }

        return $price_tax;
    }

    public static function discount_calculate($product, $price) {
        $price_discount = 0;

        if(!is_null($product['discount_type'])) {
            if($product['discount_type'] == 'percent') {
                $price_discount = ($price / 100) * $product['discount'];
            } else {
                $price_discount = $product['discount'];
            }
        }

        return $price_discount;
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function get_delivery_charge($branch_id, $distance = null)
    {
        $branch = Branch::with(['delivery_charge_setup'])
        ->where(['id' => $branch_id])->first(['id', 'name', 'status']);
        
        $min_delivery_charge = $branch->delivery_charge_setup->min_delivery_charge;
        $shipping_charge_per_km = $branch->delivery_charge_setup->delivery_charge_setup_per_kilometer;
        $min_distance_for_free_delivery = $branch->delivery_charge_setup->minimum_distance_for_free_delivery;

        if($distance <= $min_distance_for_free_delivery) {
            $delivery_charge = 0;
        } else {
            $distance_delivery_charge = $shipping_charge_per_km * $distance;
            $delivery_charge = max($distance_delivery_charge, $min_delivery_charge);
        }

        return $delivery_charge;
    }

    public static function product_data_formatting($data) {
        $data['category_ids'] = gettype($data['category_ids']) != 'array' ? json_decode($data['category_ids']) : $data['category_ids'];
        $data['choice_options'] = gettype($data['choice_options']) != 'array' ? json_decode(($data['choice_options'])) : $data['choice_options'];
        $data['variations'] = gettype($data['variations']) != 'array' ? json_decode(($data['variations'])) : $data['variations'];

        return $data;
    }

    public static function get_varient(array $product_variations, array $variations)
    {
        $result = [];
        $variation_price = 0;

        foreach($variations as $k => $variation) {
            foreach($product_variations as $product_variation) {
                if(isset($variation['values']) && isset($product_variation['values']) && $product_variation['name'] == $variation['name']) {
                    $result[$k] = $product_variation;
                    $result[$k]['values'] = [];
                    foreach($product_variation['values'] as $key => $option) {
                        if(in_array($option['label'], $variation['values']['label'])) {
                            $result[$k]['values'][] = $option;
                            $variation_price += $option['optionPrice'];
                        }
                    }
                }
            }
        }

        return ['price' => $variation_price, 'variations' => $result];
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = [
                'status' => 1,
                'message' => 'Pesanan berhasil dibuat'
            ];
        } elseif ($status == 'confirmed') {
            $data = [
                'status' => 1,
                'message' => 'Pesanan telah dikonfirmasi'
            ];
        } elseif ($status == 'processing') {
            $data = [
                'status' => 1,
                'message' => 'Pesanan sedang dikemas'
            ];
        } elseif ($status == 'out_for_delivery') {
            $data = [
                'status' => 1,
                'message' => 'Pesanan sedang dalam pengiriman'
            ];
        } elseif ($status == 'delivered') {
            $data = [
                'status' => 1,
                'message' => 'Pesanan terkirim'
            ];
        } elseif ($status == 'delivery_boy_delivered') {
            
        } elseif ($status == 'del_assign') {
            $data = [
                'status' => 1,
                'message' => 'Kurir ditetapkan'
            ];
        } elseif ($status == 'ord_start') {
             
        } elseif ($status == 'returned') {
            $data = [
                'status' => 0,
                'message' => ''
            ];
        } elseif ($status == 'failed') {
            $data = [
                'status' => 0,
                'message' => ''
            ];
        } elseif ($status == 'canceled') {
            $data = [
                'status' => 0,
                'message' => ''
            ];
        } elseif ($status == 'customer_notify_message') {
            $data = [
                'status' => 0,
                'message' => ''
            ];
        } elseif ($status == 'customer_notify_message_for_time_change') {
            $data = [
                'status' => 0,
                'message' => ''
            ];
        } elseif ($status == 'add_wallet_message') {
            
        } elseif ($status == 'add_wallet_bonus_message') {
            
        } else {
           $data = [
                'status' => 0,
                'message' => ''
           ];
        }

        if($data == null || array_key_exists('status', $data) && $data['status'] == 0) {
            return 0;
        }

        return $data;
    }

    public static function text_variable_data_format($value, $user_name = null, $store_name = null, $delivery_man_name = null, $transaction_id = null, $order_id = null)
    {
        $data = $value;

        if($value) {
            if($user_name) {
                $data = str_replace("{userName}", $user_name, $data);
            }

            if($store_name) {
                $data = str_replace("{storeName}", $store_name, $data);
            }

            if($delivery_man_name) {
                $data = str_replace("{deliveryManName}", $delivery_man_name, $data);
            }

            if($order_id) {
                $data = str_replace("{orderId}", $order_id, $data);
            }
        }

        return $data;
    }

    public static function send_push_notif_to_device($fcm_token, $data, $is_deliveryman_assigned = false) {
        $post_data = [
            'message' => [
                'token' => $fcm_token,
                'data' => [
                    'title' => (string) $data['title'],
                    'body' => (string) $data['description'],
                    'image' => (string) $data['image'],
                    'order_id' => (string) $data['order_id'],
                    'type' => (string) $data['type'],
                    'is_deliveryman_assigned' => $is_deliveryman_assigned ? "1" : "0"
                ],
                'notification' => [
                    'title' => (string) $data['title'],
                    'body' => (string) $data['description']
                ]
            ]
        ];

        return self::send_notification_to_http($post_data);
    }

    public static function send_notification_to_http(array|null $data): bool|PromiseInterface|Response
    {
        return false;
    }

    public static function new_variation_price($product, $variations)
    {
        $match = $variations;
        $result = 0;

        foreach($product as $product_variation) {
            foreach($product_variation['values'] as $option) {
                foreach($match as $variation) {
                    if($product_variation['name'] == $variation['name'] && isset($variation['values']) && in_array($option['label'], $variation['values']['label'])) {
                        $result += $option['optionPrice'];
                    }
                }
            }
        }

        return $result;
    }
}