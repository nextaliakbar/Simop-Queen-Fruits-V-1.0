<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BranchPromotionController;
use App\Http\Controllers\Admin\CustomRoleController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DeliveryChargeController;
use App\Http\Controllers\Admin\OfflinePaymentMethodController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\POSController;
use App\Http\Controllers\Admin\TimeScheduleController;
use App\Models\DeliveryMan;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function(){
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function(){
        Route::get('/code/captcha/{tmp}', [LoginController::class, 'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit'])->middleware('actch');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    });

    Route::group(['middleware' => ['admin']], function(){
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('order-statistics', [DashboardController::class, 'order_statistics'])->name('order-statistics');
        Route::get('earning-statistics', [DashboardController::class, 'earning_statistics'])->name('earning-statistics');
        Route::post('order-stats', [DashboardController::class, 'order_stats'])->name('order-stats');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settings_update']);
        Route::post('settings-password', [SystemController::class, 'settings_password_update'])->name('settings-password');

        Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => ['module:Manajemen Kasir']], function() {
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
            Route::post('session-destroy', [POSController::class, 'session_destroy'])->name('session-destroy');
            Route::post('add-delivery-address', [POSController::class, 'add_delivery_info'])->name('add-delivery-address');
            Route::post('discount', [POSController::class, 'update_discount'])->name('discount');
            Route::post('order_type/store', [POSController::class, 'order_type_store'])->name('order_type.store');
            Route::get('order-details/{id}', [POSController::class, 'order_details'])->name('order-details');
            Route::get('invoice/{id}', [POSController::class, 'generate_invoice']);
        });
        
        Route::group(['prefix' => 'orders', 'as' => 'orders.', 'middleware' => ['module:Manajemen Pesanan']], function() {
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
        Route::get('verify-offline-payment/quick-view-details', [OfflinePaymentMethodController::class, 'quick_view_details'])->name('offline-modal-view');

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:Manajemen Produk']], function(){
            Route::get('add', [CategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [CategoryController::class, 'sub_index'])->name('add-sub-category');
            Route::post('store', [CategoryController::class, 'store'])->name('store'); 
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');  
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [CategoryController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('delete');
            Route::post('search', [CategoryController::class, 'search'])->name('search');
            Route::get('priority', [CategoryController::class, 'priority'])->name('priority');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => ['module:Manajemen Produk']], function(){
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::get('get-categories', [ProductController::class, 'get_categories'])->name('get-categories');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::get('recommended/{id}/{status}', [ProductController::class, 'recommended'])->name('recommended');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['actch', 'module:Manajemen Pengguna']], function(){
            Route::get('list', [CustomerController::class, 'list'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::get('update-status', [CustomerController::class, 'update_status'])->name('update_status');
            Route::delete('delete', [CustomerController::class, 'destroy'])->name('destroy');
            
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:Manajemen Produk']], function(){
            Route::get('list', [ReviewController::class, 'list'])->name('list');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:promotion_management']], function(){
            Route::get('list', [BannerController::class, 'list'])->name('list');
            Route::post('store', [BannerController::class, 'store'])->name('store');
            Route::get('status/{id}/{status}', [BannerController::class, 'status'])->name('status');
            Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:Manajemen Promosi']], function() {
            Route::get('add-new', [CouponController::class, 'index'])->name('add-new');
            Route::post('store', [CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CouponController::class, 'delete'])->name('delete');
            Route::get('generate-coupon-code', [CouponController::class, 'generate_coupon_code'])->name('generate-coupon-code');
            Route::get('coupon-details', [CouponController::class, 'coupon_details'])->name('coupon-details');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.', 'middleware' => ['module:Manajemen Pengguna']], function(){
            Route::get('list', [DeliveryManController::class, 'list'])->name('list');
            Route::get('add', [DeliveryManController::class, 'index'])->name('add');
            Route::post('store', [DeliveryManController::class, 'store'])->name('store');
            Route::get('ajax-is-active', [DeliveryManController::class, 'ajax_is_active'])->name('ajax-is-active');
            Route::get('details/{id}', [DeliveryManController::class, 'details'])->name('details');
            Route::get('edit/{id}', [DeliveryManController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [DeliveryManController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [DeliveryManController::class, 'delete'])->name('delete');

            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function(){
                Route::get('list', [DeliveryManController::class, 'review_list'])->name('list');
            });
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:Manajemen Pengguna']], function(){
            Route::get('create', [CustomRoleController::class, 'create'])->name('create');
            Route::post('create', [CustomRoleController::class, 'store'])->name('store');
            Route::get('update/{id}', [CustomRoleController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CustomRoleController::class, 'update']);
            Route::delete('delete', [CustomRoleController::class, 'delete'])->name('delete');
            Route::get('change-status/{id}', [CustomRoleController::class, 'change_status'])->name('change-status');
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:Manajemen Pengguna']], function(){
            Route::get('add-new', [EmployeeController::class, 'index'])->name('add-new');
            Route::post('add-new', [EmployeeController::class, 'store']);
            Route::get('list', [EmployeeController::class, 'list'])->name('list');
            Route::get('update/{id}', [EmployeeController::class, 'edit'])->name('update');
            Route::post('update/{id}', [EmployeeController::class, 'update']);
            Route::get('status/{id}/{status}', [EmployeeController::class, 'status'])->name('status');
            Route::delete('delete', [EmployeeController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:Manajemen Sistem']], function(){
            Route::group(['prefix' => 'store', 'as' => 'store.'], function(){
                Route::get('store-setup', [BusinessSettingsController::class, 'store_index'])->name('store-setup')->middleware('actch');
                Route::post('update-setup', [BusinessSettingsController::class, 'store_setup'])->name('update-setup')->middleware('actch');

                Route::get('main-branch-setup', [BusinessSettingsController::class, 'main_branch_setup'])->name('main-branch-setup')->middleware('actch');

                Route::get('time-shcedule', [TimeScheduleController::class, 'time_schedule_index'])->name('time-schedule-index');
                Route::post('add-time-schedule', [TimeScheduleController::class, 'add_schedule'])->name('time-schedule-add');
                Route::get('time-schedule-remove', [TimeScheduleController::class, 'remove_schedule'])->name('time-schedule-remove');

                Route::get('delivery-fee-setup', [DeliveryChargeController::class, 'delivery_fee_setup'])->name('delivery-fee-setup')->middleware('actch');
                Route::post('store-kilometer-wise-delivery-charge', [DeliveryChargeController::class, 'store_km_wise_delivery_charge'])->name('store-kilometer-wise-delivery-charge')->middleware('actch');
                Route::get('check-distance-based-delivery', [DeliveryChargeController::class, 'check_distance_based_delivery'])->name('check-distance-based-delivery');

                Route::get('order-index', [BusinessSettingsController::class, 'order_index'])->name('order-index');
                Route::post('order-update', [BusinessSettingsController::class, 'order_update'])->name('order-update');
            });

            Route::group(['prefix' => 'web-app', 'as' => 'web-app.', 'middleware' => ['module:Manajemen Sistem']], function(){
                Route::group(['prefix' => 'third-party', 'as' => 'third-party.', 'middleware' => ['module:Manajemen Sistem']], function() {
                    Route::group(['prefix' => 'offline-payment', 'as' => 'offline-payment.'], function() {
                        Route::get('list', [OfflinePaymentMethodController::class, 'list'])->name('list');
                        Route::get('add', [OfflinePaymentMethodController::class, 'add'])->name('add');
                        Route::post('store', [OfflinePaymentMethodController::class, 'store'])->name('store');
                        Route::get('status/{id}/{status}', [OfflinePaymentMethodController::class, 'status'])->name('status');
                        Route::get('edit/{id}', [OfflinePaymentMethodController::class, 'edit'])->name('edit');
                        Route::post('update/{id}', [OfflinePaymentMethodController::class, 'update'])->name('update');
                        Route::post('delete', [OfflinePaymentMethodController::class, 'delete'])->name('delete');
                    });
                });
            });
        });

        Route::group(['prefix' => 'branch', 'as' => 'branch.', 'middleware' => ['module:Manajemen Sistem']], function (){
            Route::get('add-new', [BranchController::class, 'index'])->name('add-new');
            Route::post('store', [BranchController::class, 'store'])->name('store');
            Route::get('list', [BranchController::class, 'list'])->name('list');
            Route::get('edit/{id}', [BranchController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [BranchController::class, 'update'])->name('update');
            Route::get('status/{id}', [BranchController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BranchController::class, 'delete'])->name('delete'); 
        });

        Route::group(['prefix' => 'promotion', 'as' => 'promotion.', 'middleware' => ['module:Manajemen Sistem']], function() {
            Route::get('status/{id}/{status}', [BranchPromotionController::class, 'status'])->name('status');
        });
    });
});