<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SystemController;
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
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settings_update']);
        Route::post('settings-password', [SystemController::class, 'settings_password_update'])->name('settings-password');

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:product_management']], function(){
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

        Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => ['module:product_management']], function(){
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

        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['actch', 'module:user_management']], function(){
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:product_management']], function(){
            Route::get('list', [ReviewController::class, 'list'])->name('list');
        });
    });
});