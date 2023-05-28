<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('shopify')->group(function() {
  // App fallback
  Route::controller(ShopifyAuthController::class)
    ->group(function() {
      Route::middleware(['shopify.installed'])->group(function() {
        Route::fallback('fallback');
      });

      // App authorization and register
      Route::get('/auth', 'auth');
      Route::get('/auth/callback', 'authCallback');
    });

  Route::controller(ShopifyWebhookController::class)
    ->group(function() {
      Route::post('/webhooks', 'handle');
    });
});

Route::controller(ShopifyWebhookController::class)
    ->group(function() {
      Route::post('/webhooks', 'handle');
    });

