<?php

namespace App\Shopify;

use Shopify\Webhooks\{
  Registry, 
  Topics
};
use Illuminate\Support\Facades\Log;
use App\Exceptions\ShopifyWebhook;

class Webhooks {

  /**
   * Register all webhooks. This can be called even if webhooks are still registered.
   * 
   * @param string $shop
   * @param string $accessToken
   * @return void
   */
  public static function register(
    string $shop, 
    string $accessToken
  ) {
    $registerActions = config("shopify.webhooks", []);
    $defaultPath = config("shopify.webhook_path", "/shopify/webhooks");
    foreach($registerActions as $action) {
      if(is_string($action)) {
        $action = [
          "topic" => Topics::$action
        ];
      }
      if(!isset($action["success"])) {
        $action["success"] = "Registered {$action["topic"]} webhook.";
      }
      if(!isset($action["error"])) {
        $action["error"] = "Failed to register {$action["topic"]} webhook for shop {$shop} with response body: ";
      }
      $response = Registry::register(
        $action["path"] ?? $defaultPath,
        $action["topic"],
        $shop,
        $accessToken
      );
      if($response->isSuccess()) {
        Log::debug($action["success"]);
      }
      else {
        Log::error($action["error"] . print_r($respone->getBody(), true));
      }
    }
  }

}