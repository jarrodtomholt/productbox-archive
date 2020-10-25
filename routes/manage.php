<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\AuthController;

/*
|--------------------------------------------------------------------------
| Tenant Api and Management Routes
|--------------------------------------------------------------------------
|
| Tenant routes are prefixed with 'tenant.manage.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'store'])->name('login');
    // Route::post('password/forgot', [ForgotPasswordController::class, 'store'])->name('forgot.password');
    // Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('reset.password');

    Route::middleware(['auth:admin', 'manage.tenant'])->group(function () {
        Route::delete('logout', [AuthController::class, 'delete'])->name('logout');
    });
});
