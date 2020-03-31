<?php

namespace IndodanaCommon;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use IndodanaCommon\IndodanaConstant;

class IndodanaConfiguration
{
  private $errors;

  public function __construct(array $config = [])
  {
    $validator = Validator::create()
      ->key('storeName', Validator::stringType()->notEmpty())
      ->key('storeUrl', Validator::domain()->notEmpty())
      ->key('storeEmail', Validator::email()->notEmpty())
      ->key('storePhone', Validator::stringType()->notEmpty()) // Respect doesn't have validation for Indonesia phone
      ->key('storeCountryCode', Validator::in(IndodanaConstant::getCountryCodes()))
      ->key('storeCity', Validator::stringType()->notEmpty())
      ->key('storeAddress', Validator::stringType()->notEmpty())
      ->key('storePostalCode', Validator::postalCode('ID')->notEmpty()) // We only validate Indonesia postal code atm (5 digits)
      ->key('apiKey', Validator::stringType()->notEmpty())
      ->key('apiSecret', Validator::stringType()->notEmpty())
      ->key('environment', Validator::in(IndodanaConstant::getEnvironments()))
      ->key('defaultOrderPendingStatus', Validator::stringType()->notEmpty())
      ->key('defaultOrderSuccessStatus', Validator::stringType()->notEmpty())
      ->key('defaultOrderFailedStatus', Validator::stringType()->notEmpty());

    $this->setErrors($validator, $config);
  }

  private function setErrors($validator, $config)
  {
    try {
      $validator->assert($config);

      $this->errors = [];
    } catch (NestedValidationException $exception) {
      $stringValidationMessage = '{{name}} must not be empty and contain text';

      // These custom error message are not perfect.
      // TODO: On next iteration, consider remove `findMessages` because on newest Respect, it's not included anymore.
      // It seems that the newest Respect force developer to validate each key on a map separately -> Need more research
      $exceptionValidationMessages = $exception->findMessages([
        'storeName'                 => $stringValidationMessage,
        'storeUrl'                  => '{{name}} must not be empty and valid URL',
        'storeEmail'                => '{{name}} must not be empty and valid email',
        'storePhone'                => $stringValidationMessage,
        'storeCountryCode'          ,
        'storeCity'                 => $stringValidationMessage,
        'storeAddress'              => $stringValidationMessage,
        'storePostalCode'           => '{{name}} must not be empty and valid Indonesia postal code',
        'apiKey'                    => $stringValidationMessage,
        'apiSecret'                 => $stringValidationMessage,
        'environment'               ,
        'defaultOrderPendingStatus' => $stringValidationMessage,
        'defaultOrderSuccessStatus' => $stringValidationMessage,
        'defaultOrderFailedStatus'  => $stringValidationMessage,
      ]);

      $frontendValidationMessages = [];

      $frontendConfigMapping = IndodanaConstant::getFrontendConfigMapping();

      foreach ($exceptionValidationMessages as $configKey => $exceptionValidationMessage) {
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
  }

  public function getValidationResult()
  {
    return [
      'errors' => $this->errors
    ];
  }
}
