<?php

namespace App\Shopify\Handlers;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;
use App\Models\Session;

class AppUninstalled implements Handler {

  public function handle(
    string $topic, 
    string $shop,
    array $body
  ): void {
    Log::debug("App was uninstalled from {$shop} - removing all sessions.");
    Session::Where("shop", $shop)->delete();
    // Add your own cleanup methods.
  }
}