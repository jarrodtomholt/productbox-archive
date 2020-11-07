<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

Route::post('login', [AuthController::class, 'store'])->name('login');
Route::post('password/forgot', [ForgotPasswordController::class, 'store'])->name('forgot.password');
Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('reset.password');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('logout', [AuthController::class, 'delete'])->name('logout');
});
