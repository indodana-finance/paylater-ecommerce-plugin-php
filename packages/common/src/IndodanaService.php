<?php

namespace IndodanaCommon;

use Respect\Validation\Validator;
use Indodana\Indodana;
use Indodana\RespectValidation\RespectValidationHelper;
use IndodanaCommon\ApiResources;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\Utils;
use IndodanaCommon\Seller;

class IndodanaService
{
  private $indodana;
  private $seller;

  private $authUtil;

  public function __construct(
    $config = [],
    $authUtil = null
  ) {
    $indodanaConfig = [];

    IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiKey');
    IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiSecret');
    IndodanaHelper::setIfExists($indodanaConfig, $config, 'environment');

    $this->indodana = new Indodana($indodanaConfig);

    if (!isset($config['seller'])) {
      throw new IndodanaCommonException('Seller is not configured.');
    }

    $this->setSeller($config['seller']);

    $this->authUtil = $authUtil ?: new Utils\Auth();
  }

  public function getIndodana()
  {
    return $this->indodana;
  }

  private function setSeller(array $input = [])
  {
    $this->seller = new Seller($input);
  }

  public function getInstallmentOptions(array $input = [], $namespace)
  {
    IndodanaLogger::info(
      sprintf(
        '%s Input: %s',
        $namespace,
        print_r($input, true)
      )
    );

    $getInstallmentOptions = new ApiResources\GetInstallmentOptions($input, $this->seller);
    $payload = $getInstallmentOptions->getPayload();

    IndodanaLogger::info(
      sprintf(
        '%s Payload: %s',
        $namespace,
        json_encode($payload)
      )
    );

    return $this->getIndodana()->getInstallmentOptions($payload);
  }

  public function checkout(array $input = [], $namespace)
  {
    $payload = $this->getCheckoutPayload($input);

    return $this->getIndodana()->checkout($payload);
  }

  public function getCheckoutPayload(array $input = [], $namespace)
  {
    IndodanaLogger::info(
      sprintf(
        '%s Input: %s',
        $namespace,
        print_r($input, true)
      )
    );

    $checkout = new ApiResources\Checkout($input, $this->seller);

    $payload = $checkout->getPayload();

    IndodanaLogger::info(
      sprintf(
        '%s Payload: %s',
        $namespace,
        json_encode($payload)
      )
    );

    return $payload;
  }

  public function isValidAuthToken($authToken)
  {
    $bearerCredentials = $this->authUtil->getBearerCredentials($authToken);

    return (
      !empty($bearerCredentials) &&
      $this->indodana->validateAuthCredentials($bearerCredentials)
    );
  }
}
