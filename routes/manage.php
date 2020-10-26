<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Manage\AuthController;
use App\Http\Controllers\Api\Manage\CategoriesController;

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

Route::middleware(['auth:admin', 'manage.tenant'])->group(function () {
    Route::get('categories', [CategoriesController::class, 'index'])->name('categories');
    Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::patch('categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
});
