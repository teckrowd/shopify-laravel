<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Shopify\Clients\HttpHeaders;
use Shopify\Webhooks\Registry;
use Illuminate\Support\Facades\Log;
use Shopify\Exception\InvalidWebhookException;

class ShopifyWebhookController extends Controller {

  public function handle(Request $request) {
    try {
      $topic = $request->header(HttpHeaders::X_SHOPIFY_TOPIC, '');

      $response = Registry::process($request->header(), $request->getContent());
      if(!$response->isSuccess()) {
        Log::error("Failed to process '${topic}' webhook: {$response->getErrorMessage()}");
        return response()->json(['message' => "Failed to process '{$topic}' webhook"], 500);
      }
    }
    catch (InvalidWebhookException $e) {
      Log::error("Got invalid webhook request for topic '{$topic}': {$e->getMessage()}");return response()->json(['message' => "Got invalid webhook request for topic '{$topic}'"], 401);
    }
    catch (\Exception $e) {
      Log::error("Got an exception when handling '{$topic}' webhook: {$e->getMessage()}");
      return response()->json(['message' => "Got an exception when handling '{$topic}' webhook"], 500);
    }
  }

}