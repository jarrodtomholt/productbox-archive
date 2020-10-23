<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Tenant routes are prefixed with 'tenant.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::get('/', function () {
    return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
})->name('home');
