<?php
class IndodanaLogger
{
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const LOG_FILE_PATH = INDODANA_LOG_DIR . 'indodana.log';

    private static function write($message, $filePath, $severity)
    {
        if (($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }

        if (($requestUri = $_SERVER['REQUEST_URI']) == '') {
            $requestUri = "REQUEST_URI_UNKNOWN";
        }

        $date = date("Y-m-d H:i:s", $time);
        $message = '[' . $severity . ']' . '[' . $date . ']' . '[' . $requestUri . ']' . $message . PHP_EOL;

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
        self::write($message, self::LOG_FILE_PATH, 'INFO');
    }

    private static function writeWarningLog($message) 
    {
        self::write($message, self::LOG_FILE_PATH, 'WARNING');
    }

    private static function writeErrorLog($message)
    {
        self::write($message, self::LOG_FILE_PATH, 'ERROR');
    }

    public static function log($logType, $message)
    {
        switch($logType) {
            case self::INFO:
                self::writeInfoLog($message);
                break;
            case self::WARNING:
                self::writeWarningLog($message);
                break;
            case self::ERROR:
                self::writeErrorLog($message);
                break;
        }
    }
}