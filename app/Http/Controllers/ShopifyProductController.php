<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Shopify\Clients\Rest;
use App\Exceptions\ShopifyProductCreatorException;
use App\Shopify\ProductCreator;

class ShopifyProductController extends Controller {
  
  public function fetchCount(Request $request) {
    $session = $request->get('shopifySession'); // Provided by the Shopify.auth middleware.

    $client = new Rest($session->getShop(), $session->getAccessToken());
    $result = $client->get('products/count');

    return response($result->getDecodedBody());
  }

  public function createProducts(Request $request) {
    $session = $request->get('shopifySession'); // Provided by the Shopify.auth middleware.

    $success = $code = $error = null;
    try {
      ProductCreator::call($session, 5);
      $success = true;
      $code = 200;
      $error = null;
    }
    catch(\Exception $e) {
      $success = false;
      if ($e instanceof ShopifyProductCreatorException) {
        $code = $e->response->getStatusCode();
        $error = $e->response->getDecodedBody();
        if (array_key_exists("errors", $error)) {
          $error = $error["errors"];
        }
      } else {
        $code = 500;
        $error = $e->getMessage();
      }
      Log::error("Failed to create products: $error");
    }
    finally {
      return response()->json(["success" => $success, "error" => $error], $code);
    }
  }

}