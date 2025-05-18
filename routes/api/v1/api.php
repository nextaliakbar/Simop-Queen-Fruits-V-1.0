<?php

use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespane' => 'Api\V1'], function() {

    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function() {
        Route::post('registration', [CustomerAuthController::class, 'registration']);
        Route::post('login', [CustomerAuthController::class, 'login']);
    });

    Route::group(['prefix' => 'config'], function() {
        Route::get('/', [ConfigController::class, 'configuration']);
        Route::get('delivery-fee', [ConfigController::class, 'deliveryFree']);
    });

    Route::group(['prefix' => 'banners', 'middleware' => 'branch_adder'], function () {
        Route::get('/', [BannerController::class, 'getBanners']);
    });

    Route::group(['prefix' => 'categories'], function() {
        Route::get('/', [CategoryController::class, 'getCategories']);
        Route::get('childes/{category_id}', [CategoryController::class, 'getChildes']);
        Route::get('products/{category_id}', [CategoryController::class, 'getProducts'])->middleware('branch_adder');
    });

    Route::group(['prefix'=>'customer', 'middleware' => ['auth:api', 'is_active']], function() {
        Route::get('info', [CustomerController::class, 'info']);
    });

    Route::group(['prefix' => 'products', 'middleware' => 'branch_adder'], function() {
        Route::get('latest', [ProductController::class, 'latestProducts']);
        Route::get('popular', [ProductController::class, 'popularProducts']);
        Route::get('set-menu', [ProductController::class, 'setMenus']);
    });

    Route::group(['prefix' => 'address'], function(){
        Route::get('list', [CustomerController::class, 'addressList'])->withoutMiddleware(['auth:api', 'is_active']);
    });
});
