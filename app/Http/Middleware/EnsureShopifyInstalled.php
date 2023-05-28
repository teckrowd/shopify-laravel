<?php

namespace App\Http\Middleware;

use App\Shopify\AuthRedirection;
use App\Models\Session;
use Closure;
use Illuminate\Http\Request;
use Shopify\Utils;

class EnsureShopifyInstalled {

  /**
   * Get the app is installed on the shop in the request.
   * 
   * @param Request $request
   * @param Closure $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next) {
    $shop = $request->query("shop", false) 
      ? Utils::sanitizeShopDomain($request->query("shop"))
      : null;
    $appInstalled = $shop 
      && Session::where("shop", $shop)
        ->whereNotNull("access_token")
        ->exists();
    $isExitingFrame = preg_match("/^ExitIframe/i", $request->path());
    return ($appInstalled || $isExitingFrame) 
      ? $next($request) 
      : AuthRedirection::redirect($request);
  }
}