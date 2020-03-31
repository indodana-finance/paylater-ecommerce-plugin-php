<?php

namespace IndodanaCommon;

use Exception;
use IndodanaCommon\IndodanaConfiguration;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\IndodanaSentry;

class IndodanaCommon
{
  private $indodanaService;

  public function __construct(array $config = [])
  {
    $namespace = '[Common-Configuration]';

    IndodanaHelper::wrapIndodanaException(
      function() use ($config, $namespace) {
        IndodanaLogger::info(
          sprintf(
            '%s Config: %s',
            $namespace,
            print_r($config, true)
          )
        );

        $this->indodanaService = new IndodanaService($config);
      },
      function() {
        throw new Exception('Invalid Indodana configuration.');
      },
      $namespace
    );
  }

  public static function validateConfiguration(array $config = [])
  {
    $indodanaConfigValidation = new IndodanaConfiguration($config);

    return $indodanaConfigValidation->getValidationResult();
  }

  public function getInstallmentOptions(array $input = [])
  {
    $namespace = '[Common-GetInstallmentOptions]';

    return IndodanaHelper::wrapIndodanaException(
      function() use ($input, $namespace) {
        $result = $this->indodanaService->getInstallmentOptions($input, $namespace);

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
        $result = $this->indodanaService->checkout($input, $namespace);

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
    $namespace = '[Common-GetCheckoutPayload]';

    $payload = IndodanaHelper::wrapIndodanaException(
      function() use ($input, $namespace){
        return $this->indodanaService->getCheckoutPayload($input, $namespace);
      },
      function() {
        throw new Exception('Something went wrong when checkout using Indodana.');
      },
      $namespace
    );

    return $payload;
  }

  public function getBaseUrl()
  {
    return $this->indodanaService->getIndodana()->getBaseUrl();
  }

  public function getAuthToken()
  {
    return $this->indodanaService->getIndodana()->getAuthToken();
  }

  public function isValidAuthToken($authToken) {
    return $this->indodanaService->isValidAuthToken($authToken);
  }

  public static function getSentryDsn($pluginName)
  {
    $namespace = '[Common-Checkout]';

    $sentryDsn = IndodanaHelper::wrapIndodanaException(
      function() use ($pluginName){
        $indodanaSentry = new IndodanaSentry();

        return $indodanaSentry->getSentryDsn($pluginName);
      },
      function() {
        throw new Exception('Invalid Sentry configuration.');
      },
      $namespace
    );

    return $sentryDsn;
  }
}
