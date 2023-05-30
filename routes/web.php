<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(ShopifyAuthController::class)->group(function() {
  Route::get(config('shopify.prefix', '/'), 'fallback')
  ->middleware('shopify.installed');
});
