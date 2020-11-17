<?php

/*
|--------------------------------------------------------------------------
| Tenant App Web Routes
|--------------------------------------------------------------------------
|
| routes are prefixed with 'app.'
| and include necessary route middleware such as identifying tenants and
| ensuring the tenant is active
|
*/

Route::get('/{any?}', function () {
    return tenant()->id;
})->where('any', '.*')->name('home');
