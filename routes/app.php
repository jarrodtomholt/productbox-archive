<?php

use Illuminate\Support\Facades\Route;
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
