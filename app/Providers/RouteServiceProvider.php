<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        parent::boot();
    }

    public function map()
    {
        $this->map_web_routes();
        $this->map_admin_routes();
        $this->map_branch_routes();
        $this->map_api_v1_routes();
    }

    protected function map_web_routes()
    {
        Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/web.php'));
    }

    protected function map_admin_routes()
    {
        Route::prefix('admin')
        ->middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/admin.php'));
    }

    protected function map_branch_routes()
    {
        Route::prefix('branch')
        ->middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/branch.php'));
    }

    protected function map_api_v1_routes()
    {
        Route::prefix('api/v1')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/api/v1/api.php'));
    }
}
