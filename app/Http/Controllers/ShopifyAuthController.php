<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Shopify\{
  Context,
  Utils
};
use App\Shopify\{
  AuthRedirection,
  Webhook
};
use App\Models\Session;
use Shopify\Auth\OAuth;

class ShopifyAuthController extends Controller {

  public function fallback(Request $request) {
    if(Context::$IS_EMBEDDED_APP && $request->query("embedded", false) === "1") {
      return file_get_contents(public_path('index.html'));
    }
    else {
      return redirect(Utils::getEmbeddedAppUrl($request->query("host", null)) . "/" . $request->path());
    }
  }

  public function auth(Request $request) {
    $shop = Utils::sanitizeShopDomain($request->query("shop"));
    // Cleanup incomplete OAuth sessions.
    Session::where("shop", $shop)->where("access_token", null)->delete();
    return AuthRedirect::redirect($request);
  }

  public function authCallback(Request $request) {
    $session = OAuth::callback(
      $request->cookie(),
      $request->query(),
      ["App\Shopify\CookieHandler", "saveShopifyCookie"]
    );

    $host = $request->query("host");
    $shop = Utils::sanitizeShopDomain($request->query("shop"));

    Webhook::register(
      $shop,
      $session->getAccessToken()
    );

    $redirectUrl = Utils::getEmbeddedAppUrl($host);

    return redirect($redirectUrl);
  }

}
