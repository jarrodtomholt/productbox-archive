<?php

use App\Http\Controllers\Api\Auth\AuthController;

Route::post('login', [AuthController::class, 'store'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('logout', [AuthController::class, 'delete'])->name('logout');
});
