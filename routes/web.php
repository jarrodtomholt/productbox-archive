<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $tenant = \App\Models\Tenant::create([
        'name' => 'Test',
        'email' => 'test@test.com',
        'phone' => '123456',
        'active' => false,
    ]);
    $tenant->domains()->create(['domain' => 'test.productbox.test']);

    return view('welcome');
});

Route::post('/', [SiteController::class, 'store'])->name('signup');
