<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\SetupController;
use App\Http\Controllers\Site\StripeConnectController;

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
    // $tenant = \App\Models\Tenant::create([
    //     'name' => 'Demo',
    //     'email' => 'demo@productbox.test',
    //     'phone' => '1234567890',
    //     'active' => true,
    // ]);
    // $tenant->domains()->create(['domain' => 'demo.productbox.test']);

    return view('welcome');
});

Route::post('/', [SiteController::class, 'store'])->name('signup');

Route::get('{tenant:slug}/setup', [SetupController::class, 'index'])->middleware('signed')->name('setup');
Route::post('{tenant:slug}/setup', [SetupController::class, 'store'])->name('setup');

Route::get('stripe/connect', [StripeConnectController::class, 'store'])->name('stripe.connect');
Route::get('{tenant:slug}/stripe/connect', [StripeConnectController::class, 'index'])->name('stripe.attempt');
