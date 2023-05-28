<?php

namespace App\Shopify;

use App\Exceptions\ShopifyBillingException;
use Illuminate\Support\Facades\Log;
use Shopify\Auth\Session;
use Shopify\Clients\Graphql;
use Shopify\Context;

class EnsureBilling {
  public const INTERVAL_ONE_TIME = "ONE_TIME";
  public const INTERVAL_EVERY_30_DAYS = "EVERY_30_DAYS";
  public const INTERVAL_ANNUAL = "ANNUAL";

  private const RECURRING_PURCHASES_QUERY = <<<'QUERY'
    query appSubscription {
      currentAppInstallation {
        activeSubscriptions {
          name, test
        }
      }
    }
  QUERY;

  private const ONE_TIME_PURCHASES_QUERY = <<<'QUERY'
    query appPurchases($endCursor: String) {
      currentAppInstallation {
        oneTimePurchases(first: 250, sortKey: CREATED_AT, after: $endCursor) {
          edges {
            node {
              name, 
              test, 
              status
            }
          }
          pageInfo {
            hasNextPage, endCursor
          }
        }
      }
    }
  QUERY;

  private const RECURRING_PURCHASE_MUTATION = <<<'QUERY'
    mutation createPaymentMutation(
      $name: String!
      $lineItems: [AppSubscriptionLineItemInput!]!
      $returnUrl: URL!
      $test: Boolean
    ) {
      appSubscriptionCreate(
        name: $name
        lineItems: $lineItems
        returnUrl: $returnUrl
        test: $test
      ) {
        confirmationUrl
        userErrors {
          field, message
        }
      }
    }
  QUERY;

  private const ONE_TIME_PURCHASE_MUTATION = <<<'QUERY'
    mutation createPaymentMutation(
      $name: String!
      $price: MoneyInput!
      $returnUrl: URL!
      $test: Boolean
    ) {
      appPurchaseOneTimeCreate(
        name: $name
        price: $price
        returnUrl: $returnUrl
        test: $test
      ) {
        confirmationUrl
        userErrors {
          field, message
        }
      }
    }
  QUERY;

  private static $RECURRING_INTERVALS = [
    self::INTERVAL_EVERY_30_DAYS, self::INTERVAL_ANNUAL
  ];

  /**
   * Check if the given session has an active payment.
   * 
   * @param Session $session
   * @param array $config
   * @return array
   */
  public static function check(
    Session $session,
    array $config
  ): array {
    $confirmationUrl = null;
    if(self::hasActivePayment($session, $config)) {
      $hasPayment = true;
    }
    else {
      $hasPayment = false;
      $confirmationUrl = self::requestPayment($session, $config);
    }

    return [$hasPayment, $confirmationUrl];
  }

  /**
   * Check if there is an active payment on the session.
   * 
   * @param Session $session
   * @param array $config
   * @return array
   */
  private function hasActivePayment(
    Session $session,
    array $config
  ): bool {
    if(self::isRecurring($config)) {
      return self::hasSubscription($session, $config);
    }
    else {
      return self::hasOneTimePayment($session, $config);
    }
  }

  private static function hasSubscription(
    Session $session,
    array $config
  ): bool {
    $responseBody = self::queryOrException($session, self::RECURRING_PURCHASES_QUERY);
    $subscriptions = $repsonseBody["data"]["currentAppInstallation"]["activeSubscriptions"];

    foreach($subscriptions as $subscription) {
      if($subscription["name"] === $config["chargeName"]
        && (!self::isProd() || !$subscription["test"])) {
          return true;
        }
    }

    return false;
  }

  private static function hasOneTimePayment(
    Session $session, 
    array $config
  ): bool {
    $purchases = null;
    $endCursor = null;
    do {
      $responseBody = self::queryOrException(
        $session,
        [
          "query" => self::ONE_TIME_PURCHASES_QUERY,
          "variables" => ["endCursor" => $endCursor]
        ]
      );
      $purchases = $responseBody["data"]["currentAppInstallation"]["oneTimePurchases"];

      foreach ($purchases["edges"] as $purchase) {
        $node = $purchase["node"];
        if ($node["name"] === $config["chargeName"] 
          && (!self::isProd() || !$node["test"]) 
          && $node["status"] === "ACTIVE") {
            return true;
        }
      }
      $endCursor = $purchases["pageInfo"]["endCursor"];
    } 
    while ($purchases["pageInfo"]["hasNextPage"]);

    return false;
  }

  private static function requestPayment(
    Session $session,
    array $config
  ) {
    $hostName = Context::$HOST_NAME;
    $shop = $session->getShop();
    $host = base64_encode("$shop/admin");
    $returnUrl = "https://{$hostName}?shop={$shop}&host={$host}";

    if (self::isRecurring($config)) {
      $data = self::requestRecurringPayment($session, $config, $returnUrl);
      $data = $data["data"]["appSubscriptionCreate"];
    } 
    else {
      $data = self::requestOneTimePayment($session, $config, $returnUrl);
      $data = $data["data"]["appPurchaseOneTimeCreate"];
    }

    if (!empty($data["userErrors"])) {
      throw new ShopifyBillingException(
        "Error while billing the store", 
        $data["userErrors"]
      );
    }

    return $data["confirmationUrl"];
  }

  private static function requestRecurringPayment(
    Session $session, 
    array $config, 
    string $returnUrl
  ): array {
    return self::queryOrException(
      $session,
      [
        "query" => self::RECURRING_PURCHASE_MUTATION,
        "variables" => [
          "name" => $config["chargeName"],
          "lineItems" => [
            "plan" => [
              "appRecurringPricingDetails" => [
                "interval" => $config["interval"],
                "price" => [
                  "amount" => $config["amount"], 
                  "currencyCode" => $config["currencyCode"]
                ],
              ],
            ],
          ],
          "returnUrl" => $returnUrl,
          "test" => !self::isProd(),
        ],
      ]
    );
  }

  private static function requestOneTimePayment(
    Session $session, 
    array $config, 
    string $returnUrl
  ): array {
    return self::queryOrException(
      $session,
      [
        "query" => self::ONE_TIME_PURCHASE_MUTATION,
        "variables" => [
          "name" => $config["chargeName"],
          "price" => [
            "amount" => $config["amount"], 
            "currencyCode" => $config["currencyCode"]
          ],
          "returnUrl" => $returnUrl,
          "test" => !self::isProd(),
        ],
      ]
    );
  }

  private static function isProd(): bool {
    return app()->environment() === 'production';
  }

  private static function isRecurring(
    array $config
  ): bool {
    return in_array($config["interval"], self::$RECURRING_INTERVALS);
  }

  private static function queryOrException(
    Session $session, 
    string|array $query
  ): array {
    $client = new Graphql($session->getShop(), $session->getAccessToken());

    $response = $client->query($query);
    $responseBody = $response->getDecodedBody();

    if (!empty($responseBody["errors"])) {
      throw new ShopifyBillingException("Error while billing the store", (array)$responseBody["errors"]);
    }

    return $responseBody;
  }
  
}