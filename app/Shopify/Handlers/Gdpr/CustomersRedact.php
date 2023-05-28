<?php

namespace App\Shopify\Handlers\Gdpr;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

/**
 * Store owners can request that data is deleted on behalf of a customer. When
 * this happens, Shopify invokes this webhook.
 *
 * https://shopify.dev/apps/webhooks/configuration/mandatory-webhooks#customers-redact
 */
class CustomersRedact implements Handler {
  
  public function handle(
    string $topic, 
    string $shop, 
    array $body
  ): void {
    Log::debug("Handling GDPR customer redaction request for {$shop}");
  }
}