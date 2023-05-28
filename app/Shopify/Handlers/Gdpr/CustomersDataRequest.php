<?php

namespace App\Shopify\Handlers\Gdpr;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

/**
 * Customers can request their data from a store owner. When this happens,
 * Shopify invokes this webhook.
 *
 * https://shopify.dev/apps/webhooks/configuration/mandatory-webhooks#customers-data_request
 */
class CustomersDataRequest implements Handler {
  
  public function handle(
    string $topic, 
    string $shop, 
    array $body
  ): void {
    Log::debug("Handling GDPR customer data request for {$shop}");
  }
}