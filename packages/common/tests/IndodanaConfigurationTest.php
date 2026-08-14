<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\IndodanaConfiguration;
use IndodanaCommon\IndodanaConstant;

class IndodanaConfigurationTest extends TestCase
{
  public function testInstantiatingWithEmptyConfigReturnsErrorsOnAllConfig()
  {
    $indodanaConfiguration = new IndodanaConfiguration([]);

    $validationResult = $indodanaConfiguration->getValidationResult();
    $errors = $validationResult['errors'];

    foreach(IndodanaConstant::getFrontendConfigMapping() as $key => $value) {
      $this->assertArrayHasKey($key, $errors);
    }
  }

  public function testInstantiatingWithSomeValidConfigReturnsErrorsOnlyOnInvalidConfig()
  {
    $indodanaConfiguration = new IndodanaConfiguration([
      'storeName'                 => 'Store Name',
      'storeUrl'                  => 'Store URL', // Invalid
      'storeEmail'                => 'Store Email', // Invalid
      'storePhone'                => 'Store Phone', 
      'storeCountryCode'          => 'Store Country Code', // Invalid
      'storeCity'                 => 'Store City',
      'storeAddress'              => 'Store Address',
      'storePostalCode'           => 'Store Postal Code', // Invalid
      'apiKey'                    => 'API Key',
      'apiSecret'                 => 'API Secret',
      'environment'               => 'Environment', // Invalid
      'defaultOrderPendingStatus' => 'Default Order Pending Status',
      'defaultOrderSuccessStatus' => 'Default Order Success Status',
      'defaultOrderFailedStatus'  => 'Default Order Failed Status'
    ]);

    $validationResult = $indodanaConfiguration->getValidationResult();
    $errors = $validationResult['errors'];

    $this->assertArrayNotHasKey('storeName', $errors);
    $this->assertArrayHasKey('storeUrl', $errors);
    $this->assertArrayHasKey('storeEmail', $errors);
    $this->assertArrayNotHasKey('storePhone', $errors);
    $this->assertArrayHasKey('storeCountryCode', $errors);
    $this->assertArrayNotHasKey('storeCity', $errors);
    $this->assertArrayNotHasKey('storeAddress', $errors);
    $this->assertArrayHasKey('storePostalCode', $errors);
    $this->assertArrayNotHasKey('apiKey', $errors);
    $this->assertArrayNotHasKey('apiSecret', $errors);
    $this->assertArrayHasKey('environment', $errors);
    $this->assertArrayNotHasKey('defaultOrderPendingStatus', $errors);
    $this->assertArrayNotHasKey('defaultOrderSuccessStatus', $errors);
    $this->assertArrayNotHasKey('defaultOrderFailedStatus', $errors);
  }

  public function testInstantiatingWithAllValidConfigReturnsEmptyError()
  {
    $countryCodeSample = IndodanaConstant::getCountryCodes()[0];
    $environmentSample = IndodanaConstant::getEnvironments()[0];

    $indodanaConfiguration = new IndodanaConfiguration([
      'storeName'                 => 'Store Name',
      'storeUrl'                  => 'http://storename.com',
      'storeEmail'                => 'admin@storename.com',
      'storePhone'                => 'Store Phone', 
      'storeCountryCode'          => $countryCodeSample,
      'storeCity'                 => 'Store City',
      'storeAddress'              => 'Store Address',
      'storePostalCode'           => '22222',
      'apiKey'                    => 'API Key',
      'apiSecret'                 => 'API Secret',
      'environment'               => $environmentSample,
      'defaultOrderPendingStatus' => 'Default Order Pending Status',
      'defaultOrderSuccessStatus' => 'Default Order Success Status',
      'defaultOrderFailedStatus'  => 'Default Order Failed Status'
    ]);

    $validationResult = $indodanaConfiguration->getValidationResult();
    $errors = $validationResult['errors'];

    foreach(IndodanaConstant::getFrontendConfigMapping() as $key => $value) {
      $this->assertArrayNotHasKey($key, $errors);
    }
  }
}
