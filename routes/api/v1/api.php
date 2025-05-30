<?php
use App\Http\Controllers\Api\V1\MapApiController;
use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OfflinePaymentMethodController;
use App\Http\Controllers\Api\V1\OrderController;
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
        Route::get('delivery-fee', [ConfigController::class, 'delivery_free']);
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
        Route::group(['prefix' => 'address'], function(){
            Route::get('list', [CustomerController::class, 'address_list'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('add', [CustomerController::class, 'add_address'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::put('update/{id}', [CustomerController::class, 'update_address'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::delete('delete', [CustomerController::class, 'delete_address'])->withoutMiddleware(['auth:api', 'is_active']);
        });
        Route::get('last-ordered-address', [CustomerController::class, 'last_ordered_address']);

        Route::group(['prefix' => 'order'], function() {
            Route::post('place', [OrderController::class, 'place_order'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('list', [OrderController::class, 'get_order_list'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('track-with-phone', [OrderController::class, 'track_order_with_phone'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('track-without-phone', [OrderController::class, 'track_order_without_phone'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('details-with-phone', [OrderController::class, 'order_details_with_phone'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('details-without-phone', [OrderController::class, 'order_details_without_phone'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('expenses-chart', [OrderController::class, 'expenses_chart'])->withoutMiddleware(['auth:api', 'is_active']);
        });
    });

    Route::group(['prefix' => 'products', 'middleware' => 'branch_adder'], function() {
        Route::get('latest', [ProductController::class, 'latest_products']);
        Route::get('popular', [ProductController::class, 'popular_products']);
        Route::get('import-product', [ProductController::class, 'import_products']);
        Route::get('search-recommended', [ProductController::class, 'search_recommended']);
        Route::get('recommended', [ProductController::class, 'recommended_products']);
    });

    Route::group(['prefix' => 'mapapi'], function() {
        Route::get('geocode-api', [MapApiController::class, 'geocode_api']);
        Route::get('distance-api', [MapApiController::class, 'distance_api']);
    });

    Route::group(['prefix' => 'offline-payment-method'], function() {
        Route::get('list', [OfflinePaymentMethodController::class, 'list']);
    });
});
