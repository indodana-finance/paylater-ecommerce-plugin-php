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
    try {
      return $fun();
    } catch (IndodanaCommonException $ex) {
      IndodanaLogger::error(sprintf(
        '%s Common Exception: %s',
        $namespace,
        $ex->getMessage()
      ));

      $errorHandler();
    } catch (IndodanaRequestException $ex) {
      IndodanaLogger::error(sprintf(
        '%s Request Exception: %s',
        $namespace,
        json_encode($ex->getErrorMessage())
      ));

      $errorHandler();
    } catch (IndodanaSdkException $ex) {
      IndodanaLogger::error(sprintf(
        '%s Sdk Exception: %s',
        $namespace,
        $ex->getMessage()
      ));

      $errorHandler();
    }
  }

  public static function getRequestBody()
  {
    $postData = file_get_contents('php://input');

    return json_decode($postData, true);
  }
}

