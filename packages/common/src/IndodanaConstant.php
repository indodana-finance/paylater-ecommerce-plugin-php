<?php

namespace IndodanaCommon;

use Indodana\Indodana;

/**
 * @codeCoverageIgnore We wouldn't unit test constant value
 */
class IndodanaConstant
{
  const SANDBOX = Indodana::SANDBOX_ENVIRONMENT;
  const PRODUCTION = Indodana::PRODUCTION_ENVIRONMENT;

  const LOGO_URL = 'https://indodana-web.imgix.net/assets/indodana-new-color.png?width=150&height=50';

  const DISABLED = 'DISABLED';
  const ENABLED = 'ENABLED';

  const DEFAULT_ITEM_CATEGORY = 'others';

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
    'defaultOrderFailedStatus'  => 'Default Order Failed Status'
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

  public static function getCountryCodeMapping()
  {
    return self::countryCodeMapping;
  }

  public static function getCountryCodes()
  {
    return array_keys(self::countryCodeMapping);
  }

  public static function getFrontendConfigMapping()
  {
    return self::frontendConfigMapping;
  }

  public static function getEnvironmentMapping()
  {
    return self::environmentMapping;
  }

  public static function getEnvironments()
  {
    return array_keys(self::environmentMapping);
  }

  public static function getStatusMapping()
  {
    return self::statusMapping;
  }

  public static function getSuccessTransactionStatuses()
  {
    return [ 'INITIATED', 'PAID' ];
  }
}
