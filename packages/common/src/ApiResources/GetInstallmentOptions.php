<?php

namespace IndodanaCommon\ApiResources;

use IndodanaCommon\Order;
use IndodanaCommon\Seller;

class GetInstallmentOptions
{
  private $payload;

  public function __construct(array $input = [], Seller $seller)
  {
    $order = new Order($input, $seller);

    $this->payload = [
      'amount'  => $order->getAmount(),
      'items'   => $order->getItems()
    ];
  }

  public function getPayload()
  {
    return $this->payload;
  }
}
