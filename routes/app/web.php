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
    return file_get_contents(config('nuxt.app.source'));
})->where('any', '.*')->name('home');
