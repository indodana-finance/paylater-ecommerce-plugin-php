<?php

namespace IndodanaCommon\ApiResources;

use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\Order;
use IndodanaCommon\Seller;

class Checkout
{
  // Not all CMS make postal code as required. Therefore this is the default value if postal code is not supplied
  const DEFAULT_POSTAL_CODE = '00000';

  private $payload;

  public function __construct(array $input = [], Seller $seller)
  {
    $order = new Order($input, $seller);

    // Set payload
    // ----------
    $payload = [];

    $transactionDetails = [
      'amount'  => $order->getAmount(),
      'items'   => $order->getItems()
    ];
    IndodanaHelper::setIfExists($transactionDetails, $input, 'merchantOrderId');
    $payload['transactionDetails'] = $transactionDetails;

    IndodanaHelper::setIfExists($payload, $input, 'customerDetails');

    // For merchant plugin, seller should be only 1
    $payload['sellers'] = [ $seller->getPayload() ];

    // Set billing address with default postalCode if not exists
    $billingAddress = isset($input['billingAddress']) ? $input['billingAddress'] : [];
    $payload['billingAddress'] = $this->getAddressWithPostalCode($billingAddress);

    // Set shipping address with default postalCode if not exists
    $shippingAddress = isset($input['shippingAddress']) ? $input['shippingAddress'] : [];
    $payload['shippingAddress'] = $this->getAddressWithPostalCode($shippingAddress);

    IndodanaHelper::setIfExists($payload, $input, 'paymentType');

    IndodanaHelper::setIfExists($payload, $input, 'approvedNotificationUrl');

    IndodanaHelper::setIfExists($payload, $input, 'cancellationRedirectUrl');

    IndodanaHelper::setIfExists($payload, $input, 'backToStoreUrl');

    $this->payload = $payload;
  }

  private function getAddressWithPostalCode(array $address = [])
  {
    if (!empty($address['postalCode'])) {
      return $address;
    }

    $clonedAddress = $address;

    // Set postalCode default value
    $clonedAddress['postalCode'] = self::DEFAULT_POSTAL_CODE;

    return $clonedAddress;
  }

  public function getPayload()
  {
    return $this->payload;
  }
}
