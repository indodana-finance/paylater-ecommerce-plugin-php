<?php

namespace IndodanaCommon;

use Indodana\Indodana;

class TestData
{
  const ORDER = [
    'totalAmount' => 412000,
    'products' => [
      [
        'id' => 'MAC1',
        'name' => 'MacBook Pro',
        'price' => 406000,
        'quantity' => 1,
      ]
    ],
    'shippingAmount' => 10000,
    'taxAmount' => 20000,
    'discountAmount' => 24000,
  ];

  const GET_INSTALLMENT_OPTIONS = self::ORDER;

  const CHECKOUT = self::ORDER;

  const CHECKOUT_COMPLETE = [
    'merchantOrderId' => 'KXA-1001',
    'totalAmount' => 412000,
    'products' => [
      [
        'id' => 'MAC1',
        'name' => 'MacBook Pro',
        'price' => 406000,
        'quantity' => 1,
      ]
    ],
    'shippingAmount' => 10000,
    'taxAmount' => 20000,
    'discountAmount' => 24000,
    'customerDetails' => [
      'firstName' => 'John',
      'lastName' => 'Doe',
      'email' => 'johndoe@gmail.com',
      'phone' => '081277770000'
    ],
    'billingAddress' => [
      'firstName' => 'John',
      'lastName' => 'Doe',
      'address' => 'Jl. Kyai H. Syahdan No.9, Kel.Palmerah, Kec. Palmerah',
      'city' => 'Jakarta Barat',
      'postalCode' => '11480',
      'phone' => '081277770000',
      'countryCode' => 'IDN'
    ],
    'shippingAddress' => [
      'firstName' => 'John',
      'lastName' => 'Doe',
      'address' => 'Jl. Kyai H. Syahdan No.9, Kel.Palmerah, Kec. Palmerah',
      'city' => 'Jakarta Barat',
      'postalCode' => '11480',
      'phone' => '081277770000',
      'countryCode' => 'IDN'
    ],
    'paymentType' => '30_days',
    'approvedNotificationUrl' => 'https://macofficialstore.com/indodanapayment/checkout/approve',
    'cancellationRedirectUrl' => 'https://macofficialstore.com/indodanapayment/checkout/cancel',
    'backToStoreUrl' => 'https://macofficialstore.com/indodanapayment/checkout/success'
  ];

  const SELLER = [
    'name' => 'Mac Official Store',
    'email' => 'admin@macofficialstore.com',
    'url' => 'www.macofficialstore.com',
    'address' => [
      'firstName' => 'Mac Official Store',
      'lastName' => '',
      'address' => 'Jl. M.H Thamrin Boulevard No.22',
      'city' => 'Tangerang',
      'postalCode' => '15811',
      'phone' => '081299991111',
      'countryCode' => 'IDN'
    ]
  ];

  const INDODANA_COMMON = [
    'apiKey'      => 'test',
    'apiSecret'   => 'test',
    'environment' => Indodana::SANDBOX_ENVIRONMENT,
    'seller'      => self::SELLER
  ];

  const CONFIGURATION = [
    'storeName'                 => 'Store Name',
    'storeUrl'                  => 'www.storename.com',
    'storeEmail'                => 'admin@storename.com',
    'storePhone'                => 'Store Phone', 
    'storeCountryCode'          => 'IDN',
    'storeCity'                 => 'Store City',
    'storeAddress'              => 'Store Address',
    'storePostalCode'           => '22222',
    'apiKey'                    => 'API Key',
    'apiSecret'                 => 'API Secret',
    'environment'               => 'SANDBOX',
    'defaultOrderPendingStatus' => 'Default Order Pending Status',
    'defaultOrderSuccessStatus' => 'Default Order Success Status',
    'defaultOrderFailedStatus'  => 'Default Order Failed Status'
  ];
}
