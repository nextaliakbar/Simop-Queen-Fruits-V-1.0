<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use App\Models\Review;
use Carbon\Carbon;
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

        if(!is_null($product['tax_type'])) {
            if($product['discount_type'] == 'percent') {
                $price_discount = ($price / 100) * $product['discount'];
            } else {
                $price_discount = $product['discount'];
            }
        }

        return $price_discount;
    }
}