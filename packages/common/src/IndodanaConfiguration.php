<?php

namespace IndodanaCommon;

use Indodana\Utils\Validator\Validator;
use IndodanaCommon\IndodanaConstant;

class IndodanaConfiguration
{
  private $errors;

  public function __construct(array $config = [])
  {
    $validationResult = Validator::create($config)
      ->key('storeName', Validator::required())
      ->key('storeUrl', Validator::required(), Validator::domain())
      ->key('storeEmail', Validator::required(), Validator::email())
      ->key('storePhone', Validator::required()) // Respect doesn't have validation for Indonesia phone
      ->key('storeCountryCode', Validator::required(), Validator::in(IndodanaConstant::getCountryCodes()))
      ->key('storeCity', Validator::required())
      ->key('storeAddress', Validator::required())
      ->key('storePostalCode', Validator::required(), Validator::indonesianPostalCode()) // We only validate Indonesia postal code atm (5 digits)
      ->key('apiKey', Validator::required())
      ->key('apiSecret', Validator::required())
      ->key('environment', Validator::required(), Validator::in(IndodanaConstant::getEnvironments()))
      ->key('defaultOrderPendingStatus', Validator::required())
      ->key('defaultOrderSuccessStatus', Validator::required())
      ->key('defaultOrderFailedStatus', Validator::required());

    $this->setErrors($validationResult->getErrorMessages());
  }

  private function setErrors($errors = [])
  {
    if (count($errors) === 0) {
      $this->errors = $errors;
      return;
    }

    $frontendValidationMessages = [];

    $frontendConfigMapping = IndodanaConstant::getFrontendConfigMapping();

    foreach ($errors as $configKey => $exceptionValidationMessage) {
      if (empty($exceptionValidationMessage)) {
        continue;
      }

      // We haven't handled if the value of configKey is empty
      $frontendConfigValue = $frontendConfigMapping[$configKey];

      $frontendValidationMessage = str_replace(
        $configKey,
        $frontendConfigValue,
        $exceptionValidationMessage
      );

      $frontendValidationMessages[$configKey] = $frontendValidationMessage;
    }

    $this->errors = $frontendValidationMessages;
  }

  public function getValidationResult()
  {
    return [
      'errors' => $this->errors
    ];
  }
}
