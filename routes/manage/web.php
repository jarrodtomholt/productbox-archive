<?php

/*
|--------------------------------------------------------------------------
| Tenant Manage Web Routes
|--------------------------------------------------------------------------
|
| routes are prefixed with 'manage.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::get('/manage/{any?}', function () {
})->where('any', '.*')->name('main');
