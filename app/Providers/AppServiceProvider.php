<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Shopify\DbSessionStorage;
use App\Shopify\Handlers\AppUninstalled;
use App\Shopify\Handlers\Gdpr\{
  CustomersDataRequest,
  CustomersRedact,
  ShopRedact
};
use Illuminate\Support\Facades\URL;
use Shopify\{
  Context,
  ApiVersion
};
use Shopify\Webhooks\{
  Registry,
  Topics
};

class AppServiceProvider extends ServiceProvider {

  /**
   * Register any application services.
   */
  public function register(): void {
    
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void {
    $host = str_replace("https://", "", config("app.url", "not_defined"));
    Context::initialize(
      config('shopify.api.key'),
      config('shopify.api.secret'),
      config('shopify.api.scope'),
      $host,
      new DbSessionStorage(),
      ApiVersion::LATEST,
      true, // Is embedded app,
      false, // Is private app,
      null, // Private app storefront access token,
      '', // User agent prefix
      null, // Logger interface
      config('shopify.allowed_domains', [])
    );

    URL::forceRootUrl("https://{$host}");
    URL::forceScheme("https");

    // Register the uninstall webhook handler. Add your other webhook handlers here.
    Registry::addHandler(Topics::APP_UNINSTALLED, new AppUninstalled());

    /*
     * This sets up the mandatory GDPR webhooks. You’ll need to fill in the endpoint to be used by your app in the
     * “GDPR mandatory webhooks” section in the “App setup” tab, and customize the code when you store customer data
     * in the handlers being registered below.
     *
     * More details can be found on shopify.dev:
     * https://shopify.dev/docs/apps/webhooks/configuration/mandatory-webhooks
     *
     * Note that you'll only receive these webhooks if your app has the relevant scopes as detailed in the docs.
     */
      Registry::addHandler('CUSTOMERS_DATA_REQUEST', new CustomersDataRequest());
      Registry::addHandler('CUSTOMERS_REDACT', new CustomersRedact());
      Registry::addHandler('SHOP_REDACT', new ShopRedact());
  }
}
