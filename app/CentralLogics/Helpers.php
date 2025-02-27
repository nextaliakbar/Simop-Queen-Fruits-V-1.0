<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Helpers
{
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

    public static function language_load()
    {
        if(\session()->has('language_settings')) {
            $language = \session()->get('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'language')->first();
            \session()->put('language_settings', $language);
        }

        return $language;
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

        echo $image_name;
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
}