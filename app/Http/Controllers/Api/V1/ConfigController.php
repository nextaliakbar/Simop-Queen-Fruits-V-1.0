<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\TimeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function __construct(
        private Branch $branch,
        private TimeSchedule $time_schedule,
        private BusinessSetting $business_setting
    ) {}

     public function configuration(): JsonResponse
{       $play_store_config = Helpers::get_business_settings('play_store_config');
        $app_store_lonfig = Helpers::get_business_settings('app_store_config');
        $schedules = $this->time_schedule->select('day', 'opening_time', 'closing_time')->get();
        $customer_login = [
            'login_option' => [
                'manual_login' => 1,
                'otp_login' => 0,
            ],
            'social_media_login_options' => 0
        ];
        return response()->json([
            'store_name' => Helpers::get_business_settings('store_name'),
            'store_logo' => Helpers::get_business_settings('logo'),
            'store_address' => Helpers::get_business_settings('address'),
            'store_phone' => Helpers::get_business_settings('phone'),
            'store_email' => Helpers::get_business_settings('email_address'),
            'base_urls' => [
                'product_image_url' => asset('storage/app/public/product'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'category_banner_image_url' => asset('storage/app/public/category/banner'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'store_image_url' => asset('storage/app/public/store'),
                'branch_image_url' => asset('storage/app/public/branch'),
            ],
            'self_pickup' => (boolean) Helpers::get_business_settings('self_pickup') ?? 1,
            'delivery' => (boolean) Helpers::get_business_settings('delivery') ?? 1,
            'store_location_coverage' => $this->branch->where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float) Helpers::get_business_settings('minimum_order_value'),
            'branches' => $this->branch->all(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status', 'image', 'cover_image', 'preparation_time']),
            'play_store_config' => [
                "status" => isset($play_store_config) && (boolean)$play_store_config['status'],
                "link" => isset($play_store_config) ? $play_store_config['link'] : null,
                "min_version" => isset($play_store_config) && array_key_exists('min_version', $app_store_lonfig) ? $play_store_config['min_version'] : null
            ],
            'customer_login' => $customer_login,
            'app_store_config' => [
                "status" => isset($app_store_lonfig) && (boolean)$app_store_lonfig['status'],
                "link" => isset($app_store_lonfig) ? $app_store_lonfig['link'] : null,
                "min_version" => isset($app_store_lonfig) && array_key_exists('min_version', $app_store_lonfig) ? $app_store_lonfig['min_version'] : null
            ],
            'schedule_order_slot_duration' => (int) (Helpers::get_business_settings('schedule_order_slot_duration') ?? 30),
            'store_schedule_time' => $schedules,
            'offline_payment' => 'true',
            'google_map_status' => (integer) (Helpers::get_business_settings('google_map_status') ?? 0),
        ], 200);
    }

    public function delivery_free(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $branch = $this->branch->with(['delivery_charge_setup'])
            ->where(['id' => $request['branch_id']])
            ->first(['id', 'name', 'status']);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        if (isset($branch->delivery_charge_setup) && $branch->delivery_charge_setup->delivery_charge_type == 'distance') {
            unset($branch->delivery_charge_by_area);
            $branch->delivery_charge_by_area = [];
        }

        return response()->json($branch);
    }
}
