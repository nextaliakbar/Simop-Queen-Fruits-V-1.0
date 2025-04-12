<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\Auth\LoginController;
use App\Http\Controllers\Branch\BusinessSettingsController;
use App\Http\Controllers\Branch\CustomerController;
use App\Http\Controllers\Branch\DashboardController;
use App\Http\Controllers\Branch\OfflinePaymentMethodController;
use App\Http\Controllers\Branch\OrderController;
use App\Http\Controllers\Branch\POSController;
use App\Http\Controllers\Branch\ProductController;
use App\Http\Controllers\Branch\SystemController;

Route::group(['namespace' => 'Branch', 'as' => 'branch.'], function() {
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function() {
        Route::get('/code/captcha/{tmp}', [LoginController::class, 'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit'])->middleware('actch');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => ['branch', 'branch_status']], function() {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('order-stats', [DashboardController::class, 'order_stats'])->name('order-stats');
        Route::get('order-statistics', [DashboardController::class, 'order_statistics'])->name('order-statistics');
        Route::get('earning-statistics', [DashboardController::class, 'earning_statistics'])->name('earning-statistics');
        
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settings_basic_info_update']);
        Route::post('settings-password', [SystemController::class, 'settings_password_info_update'])->name('settings-password');
    
        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function() {
            Route::get('orders', [POSController::class, 'list'])->name('orders');
            Route::get('/', [POSController::class, 'index'])->name('index');
            Route::get('quick-view', [POSController::class, 'quick_view'])->name('quick-view');
            Route::post('variant-price', [POSController::class, 'variant_price'])->name('variant_price');
            Route::post('add-to-cart', [POSController::class, 'add_to_cart'])->name('add-to-cart');
            Route::post('remove-from-cart', [POSController::class, 'remove_from_cart'])->name('remove-from-cart');
            Route::post('cart-items', [POSController::class, 'cart_items'])->name('cart-items');
            Route::post('update-quantity', [POSController::class, 'update_quantity'])->name('update-quantity');
            Route::post('empty-cart', [POSController::class, 'empty_cart'])->name('empty-cart');
            Route::get('customers', [POSController::class, 'get_customers'])->name('customers');
            Route::post('order', [POSController::class, 'order'])->name('order');
            Route::any('store-keys', [POSController::class, 'store_keys'])->name('store-keys');
            Route::post('customer-store', [POSController::class, 'customer_store'])->name('customer-store');
            Route::post('add-delivery-address', [POSController::class, 'add_delivery_info'])->name('add-delivery-address');
            Route::post('discount', [POSController::class, 'update_discount'])->name('discount');
            Route::post('order_type/store', [POSController::class, 'order_type_store'])->name('order_type.store');
            Route::get('order-details/{id}', [POSController::class, 'order_details'])->name('order-details');
            Route::get('invoice/{id}', [POSController::class, 'generate_invoice']);
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function() {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generate_invoice'])->name('generate-invoice');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'add_payment_reference_code'])->name('add-payment-ref-code');
            Route::post('update-shipping/{id}', [OrderController::class, 'update_shipping'])->name('update-shipping');
            Route::post('increase-preparation-time/{id}', [OrderController::class, 'preparation_time'])->name('increase-preparation-time');
            Route::get('payment-status', [OrderController::class, 'payment_status'])->name('payment-status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', [OrderController::class, 'add_deliveryman'])->name('add-delivery-man');
            Route::get('ajax-change-delivery-time-date/{order_id}', [OrderController::class, 'ajax_change_delivery_time_and_date'])->name('ajax-change-delivery-time-date');
            Route::get('verify-offline-payment/{order_id}/{status}', [OrderController::class, 'verify_offline_payment']);
        });

        Route::get('verify-offline-payment/{status}', [OfflinePaymentMethodController::class, 'offline_payment_list'])->name('verify-offline-payment');
        Route::get('verify-offline-payment/quick-view-details', [OfflinePaymentMethodController::class, ''])->name('offline-modal-view');

        Route::group(['prefix' => 'product', 'as' => 'product.'], function() {
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::get('set-price/{id}', [ProductController::class, 'set_price'])->name('set-price');
            Route::post('set-price-update/{id}', [ProductController::class, 'update_price'])->name('set-price-update');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function() {
            Route::get('view/{id}', [CustomerController::class, 'view'])->name('view');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function() {
            Route::get('index', [BusinessSettingsController::class, 'index'])->name('index');
            Route::post('update', [BusinessSettingsController::class, 'update'])->name('update');
        });
    });
});