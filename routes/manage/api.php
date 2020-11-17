<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\AuthController;
use App\Http\Controllers\Api\Manage\ItemsController;
use App\Http\Controllers\Api\Manage\CouponController;
use App\Http\Controllers\Api\Manage\OptionsController;
use App\Http\Controllers\Api\Manage\SettingsController;
use App\Http\Controllers\Api\Manage\VariantsController;
use App\Http\Controllers\Api\Manage\CategoriesController;
use App\Http\Controllers\Api\Manage\Auth\DevicesController;
use App\Http\Controllers\Api\Manage\ResetPasswordController;
use App\Http\Controllers\Api\Manage\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Tenant Api and Management Routes
|--------------------------------------------------------------------------
|
| Tenant routes are prefixed with 'manage.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'store'])->name('login');
    Route::post('password/forgot', [ForgotPasswordController::class, 'store'])->name('forgot.password');
    Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('reset.password');
});

Route::middleware(['auth:admin', 'manage.tenant'])->group(function () {
    Route::delete('logout', [AuthController::class, 'delete'])->name('auth.logout');
    Route::post('devices', [DevicesController::class, 'store'])->name('auth.device');

    Route::get('categories', [CategoriesController::class, 'index'])->name('categories');
    Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::patch('categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

    Route::get('items', [ItemsController::class, 'index'])->name('items');
    Route::post('items', [ItemsController::class, 'store'])->name('items.store');
    Route::patch('items/{item}', [ItemsController::class, 'update'])->name('items.update');
    Route::delete('items/{item}', [ItemsController::class, 'destroy'])->name('items.destroy');

    Route::post('items/{item}/variants', [VariantsController::class, 'store'])->name('variants.store');
    Route::patch('items/{item}/variants/{variant}', [VariantsController::class, 'update'])->name('variants.update');
    Route::delete('items/{item}/variants/{variant}', [VariantsController::class, 'destroy'])->name('variants.destroy');

    Route::post('items/{item}/options', [OptionsController::class, 'store'])->name('options.store');
    Route::patch('items/{item}/options/{option}', [OptionsController::class, 'update'])->name('options.update');
    Route::delete('items/{item}/options/{option}', [OptionsController::class, 'destroy'])->name('options.destroy');

    Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::patch('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('settings', [SettingsController::class, 'store'])->name('settings');
});
