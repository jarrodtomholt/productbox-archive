<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ClearCartController;
use App\Http\Controllers\Api\CategoriesController;

/*
|--------------------------------------------------------------------------
| Tenant App Routes
|--------------------------------------------------------------------------
|
| Tenant routes are prefixed with 'app.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::get('categories', [CategoriesController::class, 'index'])->name('categories');

Route::get('cart', [CartController::class, 'index'])->name('cart');
Route::post('cart', [CartController::class, 'store'])->name('cart');
Route::put('cart', [CartController::class, 'update'])->name('cart');
Route::delete('cart', [CartController::class, 'destroy'])->name('cart');
Route::delete('cart/clear', [ClearCartController::class, 'destroy'])->name('cart.clear');

Route::post('coupon', [CouponController::class, 'store'])->name('coupon');
Route::delete('coupon', [CouponController::class, 'destroy'])->name('coupon');

Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout');
