<?php

namespace IndodanaCommon;

use Exception;
use Indodana\Indodana;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaConfiguration;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\IndodanaSentry;

class IndodanaCommon
{
  const CONSTRUCTOR_ERROR_MESSAGE = 'Invalid Indodana configuration.';
  const GET_INSTALLMENT_OPTIONS_ERROR_MESSAGE = 'Something went wrong when getting installment options using Indodana.';
  const CHECKOUT_ERROR_MESSAGE = 'Something went wrong when checkout using Indodana.';
  const SETUP_SENTRY_ERROR_MESSAGE = 'Invalid Sentry configuration.';

  private $indodanaService;

  public function __construct(array $config = [])
  {
    $namespace = '[Common-Configuration]';

    IndodanaHelper::wrapIndodanaException(
      function() use ($config, $namespace) {
        IndodanaLogger::info(sprintf(
          '%s Config: %s',
          $namespace,
          print_r($config, true)
        ));

        $indodanaConfig = [];

        IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiKey');
        IndodanaHelper::setIfExists($indodanaConfig, $config, 'apiSecret');
        if (isset($config['environment'])) {
          $indodanaConfig['environment'] = strtoupper($config['environment']);
        }

        IndodanaLogger::info(sprintf(
          '%s Indodana Config: %s',
          $namespace,
          print_r($indodanaConfig, true)
        ));

        $indodana = new Indodana($indodanaConfig);

        if (!isset($config['seller'])) {
          throw new IndodanaCommonException('Required "seller" key not supplied in config');
        }

        $seller = new Seller($config['seller']);

        $this->setIndodanaService(
          new IndodanaService($indodana, $seller)
        );
      },
      function() {
        throw new Exception(self::CONSTRUCTOR_ERROR_MESSAGE);
      },
      $namespace
    );
  }

  public function setIndodanaService(IndodanaService $indodanaService)
  {
    $this->indodanaService = $indodanaService;
  }

  public static function validateConfiguration(array $config = [])
  {
    $indodanaConfiguration = new IndodanaConfiguration($config);

    return $indodanaConfiguration->getValidationResult();
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
        throw new Exception(self::GET_INSTALLMENT_OPTIONS_ERROR_MESSAGE);
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
        throw new Exception(self::CHECKOUT_ERROR_MESSAGE);
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
        throw new Exception(self::CHECKOUT_ERROR_MESSAGE);
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

  public function isValidAuthToken($authToken)
  {
    return $this->indodanaService->isValidAuthToken($authToken);
  }

  public static function getSentryDsn($pluginName, $indodanaSentry = null)
  {
    $usedIndodanaSentry = $indodanaSentry ?: new IndodanaSentry();

    $namespace = '[Common-Checkout]';

    $sentryDsn = IndodanaHelper::wrapIndodanaException(
      function() use ($pluginName, $usedIndodanaSentry){
        return $usedIndodanaSentry->getSentryDsn($pluginName);
      },
      function() {
        throw new Exception(self::SETUP_SENTRY_ERROR_MESSAGE);
      },
      $namespace
    );

    return $sentryDsn;
  }
}
