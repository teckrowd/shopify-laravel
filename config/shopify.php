<?php

use App\Shopify\EnsureBilling;

return [
  "webhooks" => [
    "APP_UNINSTALLED"
  ],
  "webhook_path" => "/shopify/webhooks",
  "billing" => [
    "required" => false,
    "chargeName" => "My Shopify App One-Time Billing",
    "amount" => 5.0,
    "currencyCode" => "USD",
    "interval" => EnsureBilling::INTERVAL_ONE_TIME
  ],
  'allowed_domains' => [],
  'api' => [
    'key' => env('SHOPIFY_API_KEY', 'not_defined'),
    'secret' => env('SHOPIFY_API_SECRET', 'not_defined'),
    'scope' => env('SHOPIFY_API_SCOPES', 'not_defined')
  ]
];