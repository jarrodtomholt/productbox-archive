<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\AddressController;
use App\Http\Controllers\Api\Auth\DevicesController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

Route::post('login', [AuthController::class, 'store'])->name('login');

Route::post('password/forgot', [ForgotPasswordController::class, 'store'])->name('forgot.password');
Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('reset.password');

Route::post('register', [RegisterController::class, 'store'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'index'])->name('user');
    Route::delete('logout', [AuthController::class, 'delete'])->name('logout');

    Route::post('devices', [DevicesController::class, 'store'])->name('device');

    Route::post('address', [AddressController::class, 'store'])->name('address');
    Route::delete('address', [AddressController::class, 'destroy'])->name('address');
});
