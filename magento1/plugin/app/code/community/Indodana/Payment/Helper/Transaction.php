<?php

use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaService;

class Indodana_Payment_Helper_Transaction extends Mage_Core_Helper_Abstract implements IndodanaInterface
{
  private $indodanaService;

  public function getIndodanaService()
  {
    if (!isset($this->indodanaService)) {
      $apiKey = Mage::helper('indodanapayment')->getApiKey();
      $apiSecret = Mage::helper('indodanapayment')->getApiSecret();
      $environment = Mage::helper('indodanapayment')->getEnvironment();

      $this->indodanaService = new IndodanaService([
        'apiKey'        => $apiKey,
        'apiSecret'     => $apiSecret,
        'environment'   => $environment,
        'seller'        => $this->getSeller()
      ]);
    }

    return $this->indodanaService;
  }

  public function getTotalAmount($order)
  {
    return (float) $order->getGrandTotal();
  }

  public function getTotalDiscountAmount($order)
  {
    $totalDiscountAmount = 0;

    foreach ($order->getAllVisibleItems() as $item) {
      $totalDiscountAmount += (float) $item->getDiscountAmount();
    }

    return $totalDiscountAmount;
  }

  public function getTotalShippingAmount($order)
  {
    // I'm not really sure whether to use getShippingInclTax() or getShippingAmount()

    // For get installment options
    $totalShippingAmount = (float) $order->getShippingAddress()->getShippingInclTax();

    // For checkout
    if (!$totalShippingAmount) {
      $totalShippingAmount = (float) $order->getShippingInclTax();
    }

    return $totalShippingAmount;
  }

  public function getTotalTaxAmount($order)
  {
    $totalTaxAmount = 0;

    foreach ($order->getAllVisibleItems() as $item) {
      $totalTaxAmount += (float) $item->getTaxAmount();
    }

    return $totalTaxAmount;
  }

  public function getItems($order)
  {
    // We only need parent items
    $orderItems = $order->getAllVisibleItems();

    $items = [];

    foreach($orderItems as $orderItem) {
      $product = $orderItem->getProduct();

      // For get installment options
      $quantity = $orderItem->getQty();

      // For checkout
      if (!$quantity) {
        $quantity = $orderItem->getQtyToInvoice();
      }

      $items[] = [
        'id'        => $product->getId(),
        'name'      => $product->getName(),
        'price'     => (float) $product->getPrice(),
        'url'       => $product->getProductUrl(),
        'imageUrl'  => '', // TODO: Search how to do this
        'type'      => '', // TODO: Search how to do this
        'quantity'  => $quantity
      ];
    }

    return $items;
  }

  public function getCustomerDetails($order)
  {
    $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

    // Get phone from billing address whenever possible
    $phone = $order->getBillingAddress()->getTelephone();

    // If the user didn't login, customer won't exist
    // Therefore, we will get the billing address instead
    if (!$customer->getId()) {
      $billingAddress = $order->getBillingAddress();

      return [
        'firstName' => $billingAddress->getFirstname(),
        'lastName'  => $billingAddress->getLastname(),
        'email'     => $billingAddress->getEmail(),
        'phone'     => $phone,
      ];
    }

    return [
      'firstName' => $customer->getFirstname(),
      'lastName'  => $customer->getLastname(),
      'email'     => $customer->getEmail(),
      'phone'     => $phone,
    ];
  }

  public function getBillingAddress($order)
  {
    $billingAddress  = $order->getBillingAddress();

    return [
      'firstName'     => $billingAddress->getFirstname(),
      'lastName'      => $billingAddress->getLastname(),
      'address'       => $billingAddress->getStreet(1),
      'city'          => $billingAddress->getCity(),
      'postalCode'    => $billingAddress->getPostcode(),
      'phone'         => $billingAddress->getTelephone(),
      'countryCode'   => $this->countryCode($billingAddress->getCountry())
    ];
  }

  public function getShippingAddress($order)
  {
    // Shipping address always exist even though the user ship to billing address
    $shippingAddress  = $order->getShippingAddress();

    return [
      'firstName'     => $shippingAddress->getFirstname(),
      'lastName'      => $shippingAddress->getLastname(),
      'address'       => $shippingAddress->getStreet(1),
      'city'          => $shippingAddress->getCity(),
      'postalCode'    => $shippingAddress->getPostcode(),
      'phone'         => $shippingAddress->getTelephone(),
      'countryCode'   => $this->countryCode($shippingAddress->getCountry())
    ];
  }

  public function getSeller()
  {
    $sellerName = Mage::helper('indodanapayment')->getStoreName();

    return [
      'name'    => $sellerName,
      'email'   => Mage::helper('indodanapayment')->getStoreEmail(),
      'url'     => Mage::helper('indodanapayment')->getStoreUrl(),
      'address' => [
        'firstName'   => $sellerName,
        'phone'       => Mage::helper('indodanapayment')->getStorePhone(),
        'address'     => Mage::helper('indodanapayment')->getStoreAddress(),
        'city'        => Mage::helper('indodanapayment')->getStoreCity(),
        'postalCode'  => Mage::helper('indodanapayment')->getStorePostalCode(),
        'countryCode' => Mage::helper('indodanapayment')->getStoreCountryCode()
      ]
    ];
  }

  public function getInstallmentOptions($order)
  {
    return $this->getIndodanaService()->getInstallmentOptions([
      'totalAmount'    => $this->getTotalAmount($order),
      'discountAmount' => $this->getTotalDiscountAmount($order),
      'shippingAmount' => $this->getTotalShippingAmount($order),
      'taxAmount'      => $this->getTotalTaxAmount($order),
      'items'          => $this->getItems($order)
    ]);
  }

  public function getOrderData($order)
  {
    $approvedNotificationUrl = Mage::getUrl('indodanapayment/checkout/notify');
    $cancellationRedirectUrl = Mage::getUrl('indodanapayment/checkout/cancel');
    $backToStoreUrl = Mage::getUrl('indodanapayment/checkout/success');

    // DEV MODE
    // $approvedNotificationUrl = 'https://example.com/indodanapayment/checkout/notify';
    // $cancellationRedirectUrl = 'https://example.com/indodanapayment/checkout/cancel';
    // $backToStoreUrl = 'https://example.com/indodanapayment/checkout/success';

    return $this->getIndodanaService()->getCheckoutPayload([
      'merchantOrderId'         => $order->getId(),
      'totalAmount'             => $this->getTotalAmount($order),
      'discountAmount'          => $this->getTotalDiscountAmount($order),
      'shippingAmount'          => $this->getTotalShippingAmount($order),
      'taxAmount'               => $this->getTotalTaxAmount($order),
      'items'                   => $this->getItems($order),
      'customerDetails'         => $this->getCustomerDetails($order),
      'billingAddress'          => $this->getBillingAddress($order),
      'shippingAddress'         => $this->getShippingAddress($order),
      'approvedNotificationUrl' => $approvedNotificationUrl,
      'cancellationRedirectUrl' => $cancellationRedirectUrl,
      'backToStoreUrl'          => $backToStoreUrl
    ]);
  }

  private function countryCode($country_code)
  {
    // 3 digits country codes
    $cc_three = array(
      'AF' => 'AFG',
      'AX' => 'ALA',
      'AL' => 'ALB',
      'DZ' => 'DZA',
      'AD' => 'AND',
      'AO' => 'AGO',
      'AI' => 'AIA',
      'AQ' => 'ATA',
      'AG' => 'ATG',
      'AR' => 'ARG',
      'AM' => 'ARM',
      'AW' => 'ABW',
      'AU' => 'AUS',
      'AT' => 'AUT',
      'AZ' => 'AZE',
      'BS' => 'BHS',
      'BH' => 'BHR',
      'BD' => 'BGD',
      'BB' => 'BRB',
      'BY' => 'BLR',
      'BE' => 'BEL',
      'PW' => 'PLW',
      'BZ' => 'BLZ',
      'BJ' => 'BEN',
      'BM' => 'BMU',
      'BT' => 'BTN',
      'BO' => 'BOL',
      'BQ' => 'BES',
      'BA' => 'BIH',
      'BW' => 'BWA',
      'BV' => 'BVT',
      'BR' => 'BRA',
      'IO' => 'IOT',
      'VG' => 'VGB',
      'BN' => 'BRN',
      'BG' => 'BGR',
      'BF' => 'BFA',
      'BI' => 'BDI',
      'KH' => 'KHM',
      'CM' => 'CMR',
      'CA' => 'CAN',
      'CV' => 'CPV',
      'KY' => 'CYM',
      'CF' => 'CAF',
      'TD' => 'TCD',
      'CL' => 'CHL',
      'CN' => 'CHN',
      'CX' => 'CXR',
      'CC' => 'CCK',
      'CO' => 'COL',
      'KM' => 'COM',
      'CG' => 'COG',
      'CD' => 'COD',
      'CK' => 'COK',
      'CR' => 'CRI',
      'HR' => 'HRV',
      'CU' => 'CUB',
      'CW' => 'CUW',
      'CY' => 'CYP',
      'CZ' => 'CZE',
      'DK' => 'DNK',
      'DJ' => 'DJI',
      'DM' => 'DMA',
      'DO' => 'DOM',
      'EC' => 'ECU',
      'EG' => 'EGY',
      'SV' => 'SLV',
      'GQ' => 'GNQ',
      'ER' => 'ERI',
      'EE' => 'EST',
      'ET' => 'ETH',
      'FK' => 'FLK',
      'FO' => 'FRO',
      'FJ' => 'FJI',
      'FI' => 'FIN',
      'FR' => 'FRA',
      'GF' => 'GUF',
      'PF' => 'PYF',
      'TF' => 'ATF',
      'GA' => 'GAB',
      'GM' => 'GMB',
      'GE' => 'GEO',
      'DE' => 'DEU',
      'GH' => 'GHA',
      'GI' => 'GIB',
      'GR' => 'GRC',
      'GL' => 'GRL',
      'GD' => 'GRD',
      'GP' => 'GLP',
      'GT' => 'GTM',
      'GG' => 'GGY',
      'GN' => 'GIN',
      'GW' => 'GNB',
      'GY' => 'GUY',
      'HT' => 'HTI',
      'HM' => 'HMD',
      'HN' => 'HND',
      'HK' => 'HKG',
      'HU' => 'HUN',
      'IS' => 'ISL',
      'IN' => 'IND',
      'ID' => 'IDN',
      'IR' => 'RIN',
      'IQ' => 'IRQ',
      'IE' => 'IRL',
      'IM' => 'IMN',
      'IL' => 'ISR',
      'IT' => 'ITA',
      'CI' => 'CIV',
      'JM' => 'JAM',
      'JP' => 'JPN',
      'JE' => 'JEY',
      'JO' => 'JOR',
      'KZ' => 'KAZ',
      'KE' => 'KEN',
      'KI' => 'KIR',
      'KW' => 'KWT',
      'KG' => 'KGZ',
      'LA' => 'LAO',
      'LV' => 'LVA',
      'LB' => 'LBN',
      'LS' => 'LSO',
      'LR' => 'LBR',
      'LY' => 'LBY',
      'LI' => 'LIE',
      'LT' => 'LTU',
      'LU' => 'LUX',
      'MO' => 'MAC',
      'MK' => 'MKD',
      'MG' => 'MDG',
      'MW' => 'MWI',
      'MY' => 'MYS',
      'MV' => 'MDV',
      'ML' => 'MLI',
      'MT' => 'MLT',
      'MH' => 'MHL',
      'MQ' => 'MTQ',
      'MR' => 'MRT',
      'MU' => 'MUS',
      'YT' => 'MYT',
      'MX' => 'MEX',
      'FM' => 'FSM',
      'MD' => 'MDA',
      'MC' => 'MCO',
      'MN' => 'MNG',
      'ME' => 'MNE',
      'MS' => 'MSR',
      'MA' => 'MAR',
      'MZ' => 'MOZ',
      'MM' => 'MMR',
      'NA' => 'NAM',
      'NR' => 'NRU',
      'NP' => 'NPL',
      'NL' => 'NLD',
      'AN' => 'ANT',
      'NC' => 'NCL',
      'NZ' => 'NZL',
      'NI' => 'NIC',
      'NE' => 'NER',
      'NG' => 'NGA',
      'NU' => 'NIU',
      'NF' => 'NFK',
      'KP' => 'MNP',
      'NO' => 'NOR',
      'OM' => 'OMN',
      'PK' => 'PAK',
      'PS' => 'PSE',
      'PA' => 'PAN',
      'PG' => 'PNG',
      'PY' => 'PRY',
      'PE' => 'PER',
      'PH' => 'PHL',
      'PN' => 'PCN',
      'PL' => 'POL',
      'PT' => 'PRT',
      'QA' => 'QAT',
      'RE' => 'REU',
      'RO' => 'SHN',
      'RU' => 'RUS',
      'RW' => 'EWA',
      'BL' => 'BLM',
      'SH' => 'SHN',
      'KN' => 'KNA',
      'LC' => 'LCA',
      'MF' => 'MAF',
      'SX' => 'SXM',
      'PM' => 'SPM',
      'VC' => 'VCT',
      'SM' => 'SMR',
      'ST' => 'STP',
      'SA' => 'SAU',
      'SN' => 'SEN',
      'RS' => 'SRB',
      'SC' => 'SYC',
      'SL' => 'SLE',
      'SG' => 'SGP',
      'SK' => 'SVK',
      'SI' => 'SVN',
      'SB' => 'SLB',
      'SO' => 'SOM',
      'ZA' => 'ZAF',
      'GS' => 'SGS',
      'KR' => 'KOR',
      'SS' => 'SSD',
      'ES' => 'ESP',
      'LK' => 'LKA',
      'SD' => 'SDN',
      'SR' => 'SUR',
      'SJ' => 'SJM',
      'SZ' => 'SWZ',
      'SE' => 'SWE',
      'CH' => 'CHE',
      'SY' => 'SYR',
      'TW' => 'TWN',
      'TJ' => 'TJK',
      'TZ' => 'TZA',
      'TH' => 'THA',
      'TL' => 'TLS',
      'TG' => 'TGO',
      'TK' => 'TKL',
      'TO' => 'TON',
      'TT' => 'TTO',
      'TN' => 'TUN',
      'TR' => 'TUR',
      'TM' => 'TKM',
      'TC' => 'TCA',
      'TV' => 'TUV',
      'UG' => 'UGA',
      'UA' => 'UKR',
      'AE' => 'ARE',
      'GB' => 'GBR',
      'US' => 'USA',
      'UY' => 'URY',
      'UZ' => 'UZB',
      'VU' => 'VUT',
      'VA' => 'VAT',
      'VE' => 'VEN',
      'VN' => 'VNM',
      'WF' => 'WLF',
      'EH' => 'ESH',
      'WS' => 'WSM',
      'YE' => 'YEM',
      'ZM' => 'ZMB',
      'ZW' => 'ZWE',
    );

    // Check if country code exists
    if (isset($cc_three[$country_code]) && $cc_three[$country_code] != '') {
      $country_code = $cc_three[$country_code];
    }

    return $country_code;
  }
}
