<?php

namespace IndodanaCommon;

use Exception;

class IndodanaLogger
{
  private static function validateLogDirConstant()
  {
    $logDirConstant = 'INDODANA_LOG_DIR';

    if (!defined($logDirConstant)) {
      throw new Exception("\"${logDirConstant}\" is not configured");
    }
  }

  private static function createLogMessage($message)
  {
    if (($time = $_SERVER['REQUEST_TIME']) === '') {
      $time = time();
    }

    if (($requestUri = $_SERVER['REQUEST_URI']) === '') {
      $requestUri = 'REQUEST_URI_UNKNOWN';
    }

    $date = date("Y-m-d H:i:s", $time);
   
    return "[INDODANA][${date}][${requestUri}]${message}" . PHP_EOL;
  }

  private static function write($message, $filePath)
  {
    $fd = fopen($filePath, 'a');

    if (!is_writable($filePath)) {
      throw new Exception('Unable to write to ' . $filePath);
    }

    if (!$fd) {
      throw new Exception('Unable to open log ' . $filePath);
    }

    $result  = fputs($fd, $message);

    fclose($fd);
  }

  public static function info($message)
  {
    self::validateLogDirConstant();

    $message = self::createLogMessage($message);

    $logFilePath = INDODANA_LOG_DIR . 'info.log';

    self::write($message, $logFilePath);
  }

  public static function warning($message)
  {
    self::validateLogDirConstant();

    $message = self::createLogMessage($message);

    $logFilePath = INDODANA_LOG_DIR . 'warning.log';

    self::write($message, $logFilePath);
  }

  public static function error($message)
  {
    self::validateLogDirConstant();

    $message = self::createLogMessage($message);

    $logFilePath = INDODANA_LOG_DIR . 'error.log';

    self::write($message, $logFilePath);
  }
}
