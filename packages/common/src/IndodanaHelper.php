<?php

namespace IndodanaCommon;

use Exception;
use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaLogger;

class IndodanaHelper
{
  public static function setIfExists(&$target, $source, $key)
  {
    if (isset($source[$key])) {
      $target[$key] = $source[$key];
    }
  }

  public static function wrapIndodanaException(
    $fun,
    $errorHandler,
    $namespace = ''
  ) {
    $contextNamespace = "${namespace} ";

    try {
      return $fun();
    } catch (IndodanaCommonException $ex) {
      IndodanaLogger::log(
        IndodanaLogger::ERROR,
        sprintf(
          '%sCommon Exception: %s',
          $contextNamespace,
          json_encode($ex->getMessage())
        )
      );

      $errorHandler();
    } catch (IndodanaRequestException $ex) {
      IndodanaLogger::log(
        IndodanaLogger::ERROR,
        sprintf(
          '%sRequest Exception: %s',
          $contextNamespace,
          json_encode($ex->getErrorMessage())
        )
      );

      $errorHandler();
    } catch (IndodanaSdkException $ex) {
      IndodanaLogger::log(
        IndodanaLogger::ERROR,
        sprintf(
          '%sSdk Exception: %s',
          $contextNamespace,
          json_encode($ex->getMessage())
        )
      );

      $errorHandler();
    }
  }

  public static function getRequestBody()
  {
    $postData = file_get_contents('php://input');

    return json_decode($postData, true);
  }
}

