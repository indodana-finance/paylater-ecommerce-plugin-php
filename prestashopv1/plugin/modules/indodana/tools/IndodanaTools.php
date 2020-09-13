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
      $this->indodanaCommon = new IndodanaCommon\IndodanaCommon([
        'apiKey' => Configuration::get('INDODANA_API_KEY'),
        'apiSecret' => Configuration::get('INDODANA_API_SECRET'),
        'environment' => Configuration::get('INDODANA_ENVIRONMENT'),
        'seller' => $this->getSeller()
      ]);
    }

    return $this->indodanaCommon;
  }

  public function getTotalAmount($order)
  {
    return (int) $order->getOrderTotal(true, Cart::BOTH);
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
        $discount += ($product['price_without_reduction'] - $product['price_with_reduction'])
          * $product['quantity'];
      }
    }

    return (int) $discount;
  }

  public function getTotalShippingAmount($order)
  {
    return $order->getOrderTotal(true, Cart::ONLY_SHIPPING);
  }

  public function getTotalTaxAmount($order)
  {
    /**
     * Because we use `ceil()` function on product price to avoid double/float type number
     * And we got miscalculation error response:
     * `Purchase transaction amount (1609000) is different from calculated amount at list of item (1608998).`
     * I think the tricky solution is to do reverse calculation to get total tax amount
     */
    $orderTotal = $this->getTotalAmount($order);
    $discountTotal = $this->getTotalDiscountAmount($order);
    $shippingTotal = $this->getTotalShippingAmount($order);
    $productTotal = 0;

    $products = $order->getProducts();
    foreach ($products as $product) {
      $price = ceil($this->getPriceWithoutReductionWithoutTax($product));
      $qty = $product['quantity'];

      $productTotal += $price * $qty;
    }

    $taxTotal = $orderTotal + $discountTotal - $shippingTotal - $productTotal;

    return $taxTotal;
  }

  public function getProducts($order)
  {
    $items = [];
    $products = $order->getProducts();
    foreach ($products as $product) {
      $items[] = [
        'id' => $product['reference'],
        'name' => $product['name'],
        'price' => ceil($this->getPriceWithoutReductionWithoutTax($product)),
        'quantity' => $product['quantity'],
        'category'  => IndodanaCommon\IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      ];
    }

    return $items;
  }

  public function getCustomerDetails($order)
  {
    $customer = new Customer((int) $order->id_customer);
    $invoiceDetails = new Address((int) $order->id_address_invoice);

    return [
      'firstName' => $customer->firstname,
      'lastName' => $customer->lastname,
      'email' => $customer->email,
      'phone' => $invoiceDetails->phone
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
      'phone' => $invoiceDetails->phone,
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
      'phone' => $deliveryDetails->phone,
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
}
