<?php

namespace App\Shopify\Handlers\Gdpr;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

/**
 * 48 hours after a store owner uninstalls your app, Shopify invokes this
 * webhook.
 *
 * https://shopify.dev/apps/webhooks/configuration/mandatory-webhooks#shop-redact
 */
class ShopRedact implements Handler {

  public function handle(
    string $topic, 
    string $shop, 
    array $body
  ): void {
    Log::debug("Handling GDPR shop redaction request for {$shop}");
  }
}