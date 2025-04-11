<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\Auth\LoginController;
use App\Http\Controllers\Branch\BusinessSettingsController;
use App\Http\Controllers\Branch\DashboardController;
use App\Http\Controllers\Branch\OfflinePaymentMethodController;
use App\Http\Controllers\Branch\OrderController;
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

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function() {
            Route::get('index', [BusinessSettingsController::class, 'index'])->name('index');
            Route::post('update', [BusinessSettingsController::class, 'update'])->name('update');
        });
    });
});