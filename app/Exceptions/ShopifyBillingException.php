<?php

namespace App\Exceptions;

use Exception;

class ShopifyBillingException extends Exception {
  
  public function __construct(
    string $message,
    public ?array $errorData = null
  ) {
    parent::__construct($message);
  }
}