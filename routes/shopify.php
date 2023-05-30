<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
  ShopifyAuthController,
  ShopifyWebhookController,
  ShopifyProductController
};

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

Route::controller(ShopifyAuthController::class)
  ->group(function() {
    Route::middleware(['shopify.installed'])->group(function() {
      Route::get('/', function () {
        return view('embeddedApp');
      });
    });

    // App authorization and register
    Route::get('/auth', 'auth');
    Route::get('/auth/callback', 'authCallback');
  });

Route::controller(ShopifyWebhookController::class)
  ->group(function() {
    Route::post('/webhooks', 'handle');
  });

Route::controller(ShopifyProductController::class)
  ->middleware(['shopify.auth'])
  ->prefix('products')
  ->group(function() {
    Route::get('/count', 'fetchCount');
    Route::get('/create', 'createProducts');
  });

Route::controller(ShopifyAuthController::class)->group(function() {
  Route::fallback('fallback');
});
