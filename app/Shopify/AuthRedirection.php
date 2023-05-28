<?php

namespace App\Shopify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Shopify\Auth\OAuth;
use Shopify\{
  Context,
  Utils
};

/**
 * Redirect on authentication with a Shopify app.
 */
class AuthRedirection {
  
  /**
   * Get redirect.
   * 
   * @param Request $request
   * @param bool $isOnline
   * @return RedirectResponse
   */
  public static function redirect(
    Request $request, 
    bool $isOnline = false
  ): RedirectResponse {
    $shop = Utils::sanitizeShopDomain($request->query("shop", ""));
    // Check if the request is coming from an embedded Shopify app.
    if(Context::$IS_EMBEDDED_APP && $request->query("embedded", false) === "1") {
      $redirectUrl = self::clientSideRedirectUrl($shop, $request->query());
    }
    else {
      if(!$shop) {
        abort(404);
      }
      $redirectUrl = self::serverSideRedirectUrl($shop, $isOnline);
    }
    return redirect($redirectUrl);
  }

  /**
   * Get the server side redirect URI (non-embedded apps.)
   * 
   * @param string $shop
   * @param bool $isOnline
   * @return string
   */
  private static function serverSideRedirectUrl(
    string $shop,
    bool $isOnline
  ): string {
    return OAuth::begin(
      $shop,
      "/shopify/auth/callback",
      $isOnline,
      ["App\Shopify\CookieHandler", "saveShopifyCookie"]
    );
  }

  /**
   * Get the client side redirect URI. (embedded apps)
   * 
   * @param string $shop
   * @param array $query
   * @return string
   */
  private static function clientSideRedirectUrl(
    string $shop,
    array $query
  ): string {
    $appHost = Context::$HOST_NAME;
    $redirectUrl = urlencode("https://{$appHost}/shopify/auth?shop={$shop}");

    $queryString = http_build_query(
      array_merge($query, ["redirectUrl" => $redirectUrl])
    );
    return "/ExitIframe?{$queryString}";
  }

}