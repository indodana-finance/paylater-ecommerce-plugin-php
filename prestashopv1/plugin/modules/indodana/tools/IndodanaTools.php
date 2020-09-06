<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

require_once(_PS_MODULE_DIR_ . 'indodana' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Indodana' . DIRECTORY_SEPARATOR . 'Payment' . DIRECTORY_SEPARATOR . 'autoload.php');

class IndodanaTools extends Tools
{
  public static $indodanaCommon;

  public static function getIndodanaCommon()
  {
    if (!isset(self::$indodanaCommon)) {
      self::$indodanaCommon = new IndodanaCommon\IndodanaCommon([
        'apiKey' => Configuration::get('INDODANA_API_KEY'),
        'apiSecret' => Configuration::get('INDODANA_API_SECRET'),
        'environment' => Configuration::get('INDODANA_ENVIRONMENT'),
        'seller' => self::getSeller()
      ]);
    }

    return self::$indodanaCommon;
  }

  public static function getSeller()
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

  public static function getTotal($cart)
  {
    return $cart->getOrderTotal(true, Cart::BOTH);
  }

  public static function getShippingFee($cart)
  {
    return $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
  }

  public static function getDiscount($cart)
  {
    $discount = 0;
    $products = $cart->getProducts();
    foreach ($products as $product) {
      if (_PS_VERSION_ >= 1.7) {
        $discount += $product['reduction'] * $product['quantity'];
      } else {
        $discount += ($product['price_without_reduction'] - $product['price_with_reduction'])
          * $product['quantity'];
      }
    }

    return round($discount);
  }

  public static function getTax($cart)
  {
    $tax = 0;
    $deficit = 0;
    $products = $cart->getProducts();
    foreach ($products as $product) {
      $tax += ($product['price_without_reduction'] - self::getPriceWithoutReductionWithoutTax($product))
        * $product['quantity'];
      $deficit += self::getDeficit($product);
    }

    return round($tax) + $deficit;
  }

  public static function getProducts($cart)
  {
    $items = [];
    $products = $cart->getProducts();
    foreach ($products as $product) {
      $items[] = [
        'id' => $product['reference'],
        'name' => $product['name'],
        'price' => round(self::getPriceWithoutReductionWithoutTax($product)),
        'quantity' => $product['quantity'],
        'category'  => IndodanaCommon\IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      ];
    }

    return $items;
  }

  public static function getCustomerDetails($orderId)
  {
    $orderDetails = new Order((int) $orderId);
    $customer = new Customer((int) $orderDetails->id_customer);
    $invoiceDetails = new Address((int) $orderDetails->id_address_invoice);

    return [
      'firstName' => $customer->firstname,
      'lastName' => $customer->lastname,
      'email' => $customer->email,
      'phone' => $invoiceDetails->phone
    ];
  }

  public static function getBillingAddress($orderId)
  {
    $orderDetails = new Order((int) $orderId);
    $invoiceDetails = new Address((int) $orderDetails->id_address_invoice);

    return [
      'firstName' => $invoiceDetails->firstname,
      'lastName' => $invoiceDetails->lastname,
      'address' => $invoiceDetails->address1 . ' ' . $invoiceDetails->address2,
      'city' => $invoiceDetails->city,
      'postalCode' => $invoiceDetails->postcode,
      'phone' => $invoiceDetails->phone,
      'countryCode' => Configuration::get('INDODANA_STORE_COUNTRY_CODE')
    ];
  }

  public static function getDeliveryAddress($orderId)
  {
    $orderDetails = new Order((int) $orderId);
    $deliveryDetails = new Address((int)($orderDetails->id_address_delivery));

    return [
      'firstName' => $deliveryDetails->firstname,
      'lastName' => $deliveryDetails->lastname,
      'address' => $deliveryDetails->address1 . ' ' . $deliveryDetails->address2,
      'city' => $deliveryDetails->city,
      'postalCode' => $deliveryDetails->postcode,
      'phone' => $deliveryDetails->phone,
      'countryCode' => Configuration::get('INDODANA_STORE_COUNTRY_CODE')
    ];
  }

  public static function getTotalProduct($product)
  {
    return round(self::getPriceWithoutReductionWithoutTax($product) * $product['quantity']);
  }

  /**
   * sometimes when rounding up/down numbers there will be deficit
   * so that the total price calculation does not match
   */
  public static function getDeficit($product)
  {
    $total = self::getTotalProduct($product);

    return $total % 2 ? floor($product['quantity'] / 2) : 0;
  }

  public static function getPriceWithoutReductionWithoutTax($product)
  {
    return $product['price_without_reduction'] / (($product['rate'] + 100) / 100);
  }
}
