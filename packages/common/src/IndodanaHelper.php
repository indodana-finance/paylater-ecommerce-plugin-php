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

  public static function getRequestHeaders()
  {
    $headers = [];

    foreach ($_SERVER as $name => $value)
    {
       if (substr($name, 0, 5) == 'HTTP_')
       {
           $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
       }
    }

    return $headers;
  }

  public static function getAuthToken($requestHeaders, $namespace = '') {
    $defaultAuthToken = '';

    if (isset($requestHeaders['Authorization'])) {
      $requestHeaderAuthorization = $requestHeaders['Authorization'];

      IndodanaLogger::info(sprintf(
        '%s Using request header Authorization: %s',
        $namespace,
        $requestHeaderAuthorization
      ));

      return $requestHeaderAuthorization;
    }

    if (isset($requestHeaders['X-Signature'])) {
      $requestHeaderXSignature = $requestHeaders['X-Signature'];

      IndodanaLogger::info(sprintf(
        '%s Using request header X Signature: %s',
        $namespace,
        $requestHeaderXSignature
      ));

      return $requestHeaderXSignature;
    }

    IndodanaLogger::info(sprintf(
      '%s Using default auth token: %s',
      $namespace,
      $defaultAuthToken
    ));

    return $defaultAuthToken;
  }
}
