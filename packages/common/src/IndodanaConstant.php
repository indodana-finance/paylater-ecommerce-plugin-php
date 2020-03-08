<?php

namespace IndodanaCommon;

class IndodanaConstant
{
  const SANDBOX = 'SANDBOX';
  const PRODUCTION = 'PRODUCTION';

  const DISABLED = 'DISABLED';
  const ENABLED = 'ENABLED';

  const INITIATED = 'INITIATED';

  const frontendConfigMapping = [
    'storeName'                 => 'Store Name',
    'storeUrl'                  => 'Store URL',
    'storeEmail'                => 'Store Email',
    'storePhone'                => 'Store Phone',
    'storeCountryCode'          => 'Store Country Code',
    'storeCity'                 => 'Store City',
    'storeAddress'              => 'Store Address',
    'storePostalCode'           => 'Store Postal Code',
    'apiKey'                    => 'API Key',
    'apiSecret'                 => 'API Secret',
    'environment'               => 'Environment',
    'defaultOrderPendingStatus' => 'Default Order Pending Status',
    'defaultOrderSuccessStatus' => 'Default Order Success Status',
    'defaultOrderFailedStatus'  => 'Default Order Failed Status',
    'status'                    => 'Status',
    'sortOrder'                 => 'Sort Order',
  ];

  const environmentMapping = [
    self::SANDBOX     => 'Sandbox',
    self::PRODUCTION  => 'Production'
  ];

  const statusMapping = [
    self::DISABLED  => 'Disabled',
    self::ENABLED   => 'Enabled'
  ];

  const countryCodeMapping = [
    'IDN' => 'IDN'
  ];

  public static function getCountryCodeMapping() {
    return self::countryCodeMapping;
  }

  public static function getCountryCodes() {
    return array_keys(self::countryCodeMapping);
  }

  public static function getFrontendConfigMapping()
  {
    return self::frontendConfigMapping;
  }

  public static function getConfigKeys()
  {
    return array_keys(self::frontendConfigMapping);
  }

  public static function getEnvironmentMapping()
  {
    return self::environmentMapping;
  }

  public static function getStatusMapping()
  {
    return self::statusMapping;
  }

  public static function getStatusKeys()
  {
    return array_keys(self::statusMapping);
  }

  public static function getEnvironmentKeys()
  {
    return array_keys(self::environmentMapping);
  }
}
