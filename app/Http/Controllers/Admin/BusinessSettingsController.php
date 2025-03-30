<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BusinessSetting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class BusinessSettingsController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
        private Branch $branch
    ) {}

    private function insert_or_update_business_data($key, $value)
    {
        $business_setting = $this->business_setting->where(['key' => $key['key']])->first();
        if($business_setting) {
            $business_setting->value = $value['value'];
            $business_setting->save();
        } else {
            $this->business_setting->create(array_merge($key, $value));
        }
    }

    public function store_index(): Renderable
    {
        if(!$this->business_setting->where(['key' => 'minimum_order_value'])->first()) {
            $this->insert_or_update_business_data(['key' => 'minimum_order_value'], [
                'value' => '1'
            ]);
        }

        return view('admin-views.business-settings.store-index');
    }

    public function store_setup(Request $request): RedirectResponse
    {
        if($request->has('self_pickup')) {
            $request['self_pickup'] = 1;
        }

        if($request->has('delivery')) {
            $request['delivery'] = 1;
        }

        $request['google_map_status'] = $request->has('google_map_status') ? 1 : 0;
        $request['admin_order_notification'] = $request->has('admin_order_notification') ? 1 : 0;

        $this->insert_or_update_business_data(['key' => 'store_name'], [
            'value' => $request['store_name']
        ]);

        $this->insert_or_update_business_data(['key' => 'phone'], [
            'value' => $request['phone']
        ]);

        $this->insert_or_update_business_data(['key' => 'self_pickup'], [
            'value' => $request['self_pickup']
        ]);

        $this->insert_or_update_business_data(['key' => 'delivery'], [
            'value' => $request['delivery']
        ]);

        $this->insert_or_update_business_data(['key' => 'store_open_time'], [
            'value' => $request['store_open_time']
        ]);

        $this->insert_or_update_business_data(['key' => 'store_close_time'], [
            'value' => $request['store_close_time']
        ]);

        $this->insert_or_update_business_data(['key' => 'phone'], [
            'value' => $request['phone']
        ]);

        $this->insert_or_update_business_data(['key' => 'email_address'], [
            'value' => $request['email']
        ]);

        $this->insert_or_update_business_data(['key' => 'address'], [
            'value' => $request['address']
        ]);

        $this->insert_or_update_business_data(['key' => 'google_map_status'], [
            'value' => $request['google_map_status']
        ]);

        $this->insert_or_update_business_data(['key' => 'admin_order_notification'], [
            'value' => $request['admin_order_notification']
        ]);

        Toastr::success('Pengaturan bisnis diperbarui');
        return back();
    }

    public function main_branch_setup(): Renderable
    {
        $branch = $this->branch->find(1);
        return view('admin-views.business-settings.store.main-branch', compact('branch'));
    }

    public function order_index()
    {
        return view('admin-views.business-settings.store.order-index');
    }

    public function order_update(Request $request): RedirectResponse
    {
        $this->insert_or_update_business_data(['key' => 'minimum_order_value'], [
            'value' => $request['minimum_order_value']
        ]);

        $this->insert_or_update_business_data(['key' => 'schedule_order_slot_duration'], [
            'value' => $request['schedule_order_slot_duration']
        ]);

        Toastr::success('Pengaturan bisnis diperbarui');
        return back();
    }
}
