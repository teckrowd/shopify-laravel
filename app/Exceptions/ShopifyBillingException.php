<?php

namespace App\Exceptions;

use Exception;

class ShopifyBillingException extends Exception {
  
  public function __construct(
    public ?array $errorData = null, 
    string $message
  ) {
    parent::__construct($message);
  }
}