<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Observers\BusinessSettingObserver;
use App\Observers\CategoryObserver;
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

        Category::observe(CategoryObserver::class);

        Paginator::useBootstrap();
    }
}
