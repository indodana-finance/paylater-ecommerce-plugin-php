<?php

namespace IndodanaCommon;

use Exception;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;
use Indodana\Indodana;
use Indodana\IndodanaHttpClient;
use Indodana\RespectValidation\RespectValidationHelper;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;

Validator::with('IndodanaCommon\\Validation\\Rules');

class IndodanaService
{
  private $indodana;
  private $seller;

  public function __construct(array $config = [])
  {
    $namespace = '[Common-Configuration]';

    IndodanaHelper::wrapIndodanaException(
      function() use ($config, $namespace) {
        IndodanaLogger::log(
          IndodanaLogger::INFO,
          sprintf(
            '%s Config: %s',
            $namespace,
            print_r($config, true)
          )
        );

        $indodanaConfig = [];

        IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiKey');
        IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiSecret');
        IndodanaHelper::setIfExists($indodanaConfig, $config, 'environment');

        $this->indodana = new Indodana($indodanaConfig);

        IndodanaHelper::setIfExists($this->seller, $config, 'seller');

        if (!isset($config['seller'])) {
          throw new IndodanaCommonException('Seller is not configured');
        }

        $this->setSeller($config['seller']);

      },
      function() {
        throw new Exception('Invalid Indodana configuration.');
      },
      $namespace
    );
  }

  private function setSeller(array $args = []) {
    $validator = Validator::create()
      ->key('url', Validator::stringType()->notEmpty());

    $validationResult = RespectValidationHelper::validate($validator, $args);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaCommonException($validationResult->printErrorMessages());
    }

    $this->seller = array_merge($args, [
      'id' => md5($args['url'])
    ]);
  }

  public static function validateConfiguration(array $config = [])
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

    $namespace = '[Common-ValidateConfiguration]';

    try {
      $validator->assert($config);

      return [
        'errors' => []
      ];
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

      return [
        'errors' => $frontendValidationMessages
      ];
    }
  }

  private function getShippingFee($shippingAmount) {
    return [
      'id' => 'shippingfee',
      'url' => '',
      'name' => 'Shipping Fee',
      'price' => (float) abs($shippingAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function getTaxFee($taxAmount) {
    return [
      'id' => 'taxfee',
      'url' => '',
      'name' => 'Tax Fee',
      'price' => (float) abs($taxAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function getDiscount($discountAmount) {
    return [
      'id' => 'discount',
      'url' => '',
      'name' => 'Discount',
      'price' => (float) abs($discountAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function getItems(
    $products,
    $shippingAmount,
    $taxAmount,
    $discountAmount
  ) {
    $shippingFee = $this->getShippingFee($shippingAmount);
    $taxFee = $this->getTaxFee($taxAmount);
    $discount = $this->getDiscount($discountAmount);

    $items = array_merge($products, [
      $shippingFee,
      $taxFee,
      $discount
    ]);

    // Add seller id for each item
    foreach($items as &$item) {
      $item['parentType'] = 'SELLER';
      $item['parentId'] = $this->seller['id'];
    }

    return $items;
  }

  private function validateInput(array $input = [])
  {
    $validator = Validator::create()
      ->key('totalAmount', Validator::numberType()->notOptional())
      ->key('discountAmount', Validator::numberType()->notOptional())
      ->key('shippingAmount', Validator::numberType()->notOptional())
      ->key('taxAmount', Validator::numberType()->notOptional())
      ->key('items', Validator::arrayType()->notEmpty());

    $validationResult = RespectValidationHelper::validate($validator, $input);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaCommonException($validationResult->printErrorMessages());
    }
  }

  private function getAddressWithPostalCode(array $address = [])
  {
    if (!empty($address['postalCode'])) {
      return $address;
    }

    $clonedAddress = $address;

    // Set postalCode default value
    $clonedAddress['postalCode'] = '00000';

    return $clonedAddress;
  }

  public function getInstallmentOptions(array $input = [])
  {
    $namespace = '[Common-GetInstallmentOptions]';

    return IndodanaHelper::wrapIndodanaException(
      function() use ($input, $namespace) {
        IndodanaLogger::log(
          IndodanaLogger::INFO,
          sprintf(
            '%s Input: %s',
            $namespace,
            print_r($input, true)
          )
        );

        $this->validateInput($input);

        $items = $this->getItems(
          $input['items'],
          $input['shippingAmount'],
          $input['taxAmount'],
          $input['discountAmount']
        );

        $payload = [
          'amount' => $input['totalAmount'],
          'items' => $items
        ];

        IndodanaLogger::log(
          IndodanaLogger::INFO,
          sprintf(
            '%s Payload: %s',
            $namespace,
            json_encode($payload)
          )
        );

        $result = $this->indodana->getInstallmentOptions($payload);

        return $result['payments'];
      },
      function() {
        throw new Exception('Something went wrong when getting installment options using Indodana.');
      },
      $namespace
    );
  }

  public function checkout(array $input = [])
  {
    $namespace = '[Common-Checkout]';

    return IndodanaHelper::wrapIndodanaException(
      function() use ($input, $namespace) {
        $payload = $this->generateCheckoutPayload($input, $namespace);

        $result = $this->indodana->checkout($payload);

        return $result['redirectUrl'];
      },
      function() {
        throw new Exception('Something went wrong when checkout using Indodana.');
      },
      $namespace
    );
  }

  public function getCheckoutPayload(array $input = [])
  {
    $namespace = '[Common-Checkout]';

    $payload = IndodanaHelper::wrapIndodanaException(
      function() use ($input, $namespace){
        return $this->generateCheckoutPayload($input, $namespace);
      },
      function() {
        throw new Exception('Something went wrong when checkout using Indodana.');
      },
      $namespace
    );

    return $payload;
  }

  private function generateCheckoutPayload(array $input = [], $namespace = '')
  {
    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Input: %s',
        $namespace,
        print_r($input, true)
      )
    );

    $this->validateInput($input);

    $payload = [];

    // Set transaction details
    $items = $this->getItems(
      $input['items'],
      $input['shippingAmount'],
      $input['taxAmount'],
      $input['discountAmount']
    );

    $transactionDetails = [
      'amount' => $input['totalAmount'],
      'items' => $items
    ];

    IndodanaHelper::setIfExists($transactionDetails, $input, 'merchantOrderId');

    $payload['transactionDetails'] = $transactionDetails;

    IndodanaHelper::setIfExists($payload, $input, 'customerDetails');

    // For merchant plugin, seller should be only 1
    $payload['sellers'] = [ $this->seller ];

    // Set billing address with default postalCode if not exists
    $billingAddress = isset($input['billingAddress']) ? $input['billingAddress'] : [];
    $payload['billingAddress'] = $this->getAddressWithPostalCode($billingAddress);

    // Set shipping address with default postalCode if not exists
    $shippingAddress = isset($input['shippingAddress']) ? $input['shippingAddress'] : [];
    $payload['shippingAddress'] = $this->getAddressWithPostalCode($shippingAddress);

    IndodanaHelper::setIfExists($payload, $input, 'paymentType');
    IndodanaHelper::setIfExists($payload, $input, 'approvedNotificationUrl');
    IndodanaHelper::setIfExists($payload, $input, 'cancellationRedirectUrl');
    IndodanaHelper::setIfExists($payload, $input, 'backToStoreUrl');

    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Payload: %s',
        $namespace,
        json_encode($payload)
      )
    );

    return $payload;
  }

  public function getBaseUrl()
  {
    return $this->indodana->getBaseUrl();
  }

  public function getAuthToken()
  {
    return $this->indodana->getAuthToken();
  }

  public function isValidAuthToken($authToken) {
    $credentials = $this->getAuthCredentials($authToken);

    return (
      !empty($credentials) &&
      $this->indodana->validateAuthCredentials($credentials)
    );
  }

  private function getAuthCredentials($authToken)
  {
    if (!empty($authToken)) {
      if (preg_match('/Bearer\s(\S+)/', $authToken, $matches)) {
        return $matches[1];
      }
    }

    return null;
  }

  public static function getSentryDsn($pluginName)
  {
    $namespace = '[Common-Checkout]';

    $sentryDsn = IndodanaHelper::wrapIndodanaException(
      function() use ($pluginName, $namespace){
        $result = IndodanaHttpClient::get(
          Indodana::PRODUCTION_BASE_URL . '/public/v1/merchant-plugin/sentry',
          [],
          [ 'pluginName' => $pluginName ]
        );

        return $result['data']['sentryDsn'];
      },
      function() {
        throw new Exception('Invalid Sentry configuration.');
      },
      $namespace
    );

    return $sentryDsn;
  }
}
