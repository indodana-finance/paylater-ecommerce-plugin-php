<?php
class Logger
{
    public const ERROR = 1;
    public const WARNING = 2;
    public const INFO = 3;

    private static function write($message, $filePath)
    {
        $fd = @fopen($filePath, 'a');
        if (!is_writable($filePath)) {
            throw new Exception('Unable to write to ' . $filePath);
        }
        
        if (!$fd) {
            throw new Exception('Unable to open log ' . $filePath);
        }

        $result  = fputs($fd, $message);
        fclose($fd);
    }

    private static function writeInfoLog($message)
    {
        $logFilePath = INDODANA_LOG_DIR . 'info.log';
        self::write($message, $logFilePath);
    }

    private static function writeWarningLog($message) 
    {
        $logFilePath = INDODANA_LOG_DIR . 'warning.log';
        self::write($message, $logFilePath);
    }

    private static function writeErrorLog($message)
    {
        $logFilePath = INDODANA_LOG_DIR . 'error.log';
        self::write($message, $logFilePath);
    }

    public static function log($logType, $message)
    {
        if (($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }

        if (($requestUri = $_SERVER['REQUEST_URI']) == '') {
            $requestUri = "REQUEST_URI_UNKNOWN";
        }

        $date = date("Y-m-d H:i:s", $time);
        $message = '[' . $date . ']' . '[' . $requestUri . ']' . $message . PHP_EOL;

        switch($logType) {
            case self::INFO:
                self::writeInfoLog($message);
            case self::WARNING:
                self::writeWarningLog($message);
            case self::ERROR:
                self::writeErrorLog($message);
        }
    }
}