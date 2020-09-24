<?php

// ignore vscode's phpcs extension missing namespace error
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

require_once(_PS_MODULE_DIR_ . 'indodana' . DIRECTORY_SEPARATOR . 'autoload.php');

class IndodanaTools extends Tools implements IndodanaCommon\IndodanaInterface
{
  public $indodanaCommon;

  public function getIndodanaCommon()
  {
    if (!isset($this->indodanaCommon)) {
      if (Configuration::get('INDODANA_ENVIRONMENT') == 'PRODUCTION') {
        $apiKey = Configuration::get('INDODANA_API_KEY_PRODUCTION');
        $apiSecret = Configuration::get('INDODANA_API_KEY_PRODUCTION');
      } else {
        $apiKey = Configuration::get('INDODANA_API_KEY');
        $apiSecret = Configuration::get('INDODANA_API_SECRET');
      }

      $this->indodanaCommon = new IndodanaCommon\IndodanaCommon([
        'apiKey' => $apiKey,
        'apiSecret' => $apiSecret,
        'environment' => Configuration::get('INDODANA_ENVIRONMENT'),
        'seller' => $this->getSeller()
      ]);
    }

    return $this->indodanaCommon;
  }

  public function getTotalAmount($order)
  {
    return ceil($order->getOrderTotal(true, Cart::BOTH));
  }

  public function getTotalDiscountAmount($order)
  {
    $discount = 0;
    $products = $order->getProducts();
    foreach ($products as $product) {
      // for prestashop v1.7
      if (_PS_VERSION_ >= 1.7) {
        $discount += $product['reduction'] * $product['quantity'];
      } else {
        // for prestashop v1.6, prestashop v1.6 doesn't provide product price 'reduction'
        // we need to calculate 'reduction' manually
        $discountProduct = $product['price_without_reduction'] - $product['price_with_reduction'];
        $discountProduct = $this->convertPrecisionNumber($discountProduct);
        $discount += $discountProduct * $product['quantity'];
      }
    }

    return $this->convertPrecisionNumber($discount);
  }

  public function getTotalShippingAmount($order)
  {
    return $order->getOrderTotal(true, Cart::ONLY_SHIPPING);
  }

  public function getTotalTaxAmount($order)
  {
    $taxTotal = 0;
    $products = $order->getProducts();
    foreach ($products as $key => $product) {
      $taxProduct = $product['price_without_reduction'] - $this->getPriceWithoutReductionWithoutTax($product);
      $taxProduct = $this->convertPrecisionNumber($taxProduct);
      $taxTotal += $taxProduct * $product['quantity'];
    }

    return $this->convertPrecisionNumber($taxTotal);
  }

  public function getProducts($order)
  {
    $items = [];
    $products = $order->getProducts();
    foreach ($products as $product) {
      $items[] = [
        'id' => $product['reference'],
        'name' => $product['name'],
        'price' => $this->convertPrecisionNumber($this->getPriceWithoutReductionWithoutTax($product)),
        'quantity' => $product['quantity'],
        'category'  => IndodanaCommon\IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      ];
    }

    return $items;
  }

  /**
   * Prestashop doesn't have admin fee feature
   */
  public function getAdminFeeAmount($order)
  {
    return 0;
  }

  /**
   * Prestashop doesn't have additional fee feature
   */
  public function getAdditionalFeeAmount($order)
  {
    $orderTotal = $this->getTotalAmount($order);
    $manualTotal = $this->getManualOrderTotal($order);
    $additionalFee = $orderTotal - $manualTotal;

    return $this->convertPrecisionNumber($additionalFee);
  }

  /**
   * Prestashop doesn't have insurance fee feature
   */
  public function getInsuranceFeeAmount($order)
  {
    return 0;
  }

  public function getCustomerDetails($order)
  {
    $customer = new Customer((int) $order->id_customer);
    $invoiceDetails = new Address((int) $order->id_address_invoice);

    return [
      'firstName' => $customer->firstname,
      'lastName' => $customer->lastname,
      'email' => $customer->email,
      'phone' => $this->getPhoneNumber($invoiceDetails)
    ];
  }

  public function getBillingAddress($order)
  {
    $invoiceDetails = new Address((int) $order->id_address_invoice);

    return [
      'firstName' => $invoiceDetails->firstname,
      'lastName' => $invoiceDetails->lastname,
      'address' => $invoiceDetails->address1 . ' ' . $invoiceDetails->address2,
      'city' => $invoiceDetails->city,
      'postalCode' => $invoiceDetails->postcode,
      'phone' => $this->getPhoneNumber($invoiceDetails),
      'countryCode' => Configuration::get('INDODANA_STORE_COUNTRY_CODE')
    ];
  }

  public function getShippingAddress($order)
  {
    $deliveryDetails = new Address((int) $order->id_address_delivery);

    return [
      'firstName' => $deliveryDetails->firstname,
      'lastName' => $deliveryDetails->lastname,
      'address' => $deliveryDetails->address1 . ' ' . $deliveryDetails->address2,
      'city' => $deliveryDetails->city,
      'postalCode' => $deliveryDetails->postcode,
      'phone' => $this->getPhoneNumber($deliveryDetails),
      'countryCode' => Configuration::get('INDODANA_STORE_COUNTRY_CODE')
    ];
  }

  public function getSeller()
  {
    return [
      'name' => Configuration::get('INDODANA_STORE_NAME'),
      'email' => Configuration::get('INDODANA_STORE_EMAIL'),
      'url' => Configuration::get('INDODANA_STORE_URL'),
      'address' => [
        'firstName' => Configuration::get('INDODANA_STORE_NAME'),
        'lastName' => '',
        'address' => Configuration::get('INDODANA_STORE_ADDRESS'),
        'city' => Configuration::get('INDODANA_STORE_CITY'),
        'postalCode' => Configuration::get('INDODANA_STORE_POSTAL_CODE'),
        'phone' => Configuration::get('INDODANA_STORE_PHONE'),
        'countryCode' => Configuration::get('INDODANA_STORE_COUNTRY_CODE')
      ]
    ];
  }

  private function getPriceWithoutReductionWithoutTax($product)
  {
    return $product['price_without_reduction'] / (($product['rate'] + 100) / 100);
  }

  private function convertPrecisionNumber($value)
  {
    return (float) number_format($value, 2, '.', '');
  }

  /**
   * get a phone number
   */
  private function getPhoneNumber($details)
  {
    return $details->phone != '' ? $details->phone : $details->phone_mobile;
  }

  /**
   * calcaulate order total manually
   */
  private function getManualOrderTotal($order)
  {
    $discountTotal = $this->getTotalDiscountAmount($order);
    $shippingTotal = $this->getTotalShippingAmount($order);
    $taxTotal = $this->getTotalTaxAmount($order);
    $adminFee = $this->getAdminFeeAmount($order);
    // $additionalFee = $this->getAdditionalFeeAmount($order);
    $insuranceFee = $this->getInsuranceFeeAmount($order);
    $productTotal = 0;

    $products = $order->getProducts();
    foreach ($products as $product) {
      $price = $this->convertPrecisionNumber($this->getPriceWithoutReductionWithoutTax($product));
      $qty = $product['quantity'];

      $productTotal += $price * $qty;
    }
    $productTotal = $this->convertPrecisionNumber($productTotal);

    $manualTotal = $shippingTotal + $taxTotal + $productTotal + $adminFee + $insuranceFee - $discountTotal;

    return $this->convertPrecisionNumber($manualTotal);
  }
}
