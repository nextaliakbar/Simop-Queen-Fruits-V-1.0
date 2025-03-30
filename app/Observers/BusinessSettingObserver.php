<?php

namespace App\Observers;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Cache;

class BusinessSettingObserver
{
    private function refreshBusinessSettingsCache()
    {
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);
    }

    public function created(BusinessSetting $business_setting)
    {
        $this->refreshBusinessSettingsCache();
    }

    public function updated(BusinessSetting $business_setting)
    {
        $this->refreshBusinessSettingsCache();
    }

    public function deleted(BusinessSetting $business_setting)
    {
        $this->refreshBusinessSettingsCache();
    }

    public function restored(BusinessSetting $business_setting)
    {
        $this->refreshBusinessSettingsCache();
    }

    public function forceDeleted(BusinessSetting $business_setting)
    {
        $this->refreshBusinessSettingsCache();
    }
}
