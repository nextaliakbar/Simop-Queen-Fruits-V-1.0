<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Observers\BusinessSettingObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BusinessSetting::observe(BusinessSettingObserver::class);

        Paginator::useBootstrap();
    }
}
