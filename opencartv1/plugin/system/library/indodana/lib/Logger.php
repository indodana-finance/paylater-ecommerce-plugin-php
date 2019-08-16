<?php
class IndodanaLogger
{
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const LOG_FILE_PATH = INDODANA_LOG_DIR . 'indodana.log';

    private static function write($message)
    {
        $filePath = self::LOG_FILE_PATH;

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

    public static function read()
    {
        $filePath = self::LOG_FILE_PATH;
        return file_get_contents($filePath);
    }

    public static function generateLogMessage($message, $severity) {
        if (($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }

        if (($requestUri = $_SERVER['REQUEST_URI']) == '') {
            $requestUri = "REQUEST_URI_UNKNOWN";
        }

        $date = date("Y-m-d H:i:s", $time);
        $message = '[' . $severity . ']' . '[' . $date . ']' . '[' . $requestUri . ']' . $message . PHP_EOL;

        return $message;
    }

    private static function generateInfoLog($message)
    {
        return self::generateLogMessage($message, 'INFO');
    }

    private static function generateWarningLog($message) 
    {
        return self::generateLogMessage($message, 'WARNING');
    }

    private static function generateErrorLog($message)
    {
        return self::generateLogMessage($message, 'ERROR');
    }

    public static function log($logType, $message)
    {
        $logContent = '';

        switch($logType) {
            case self::INFO:
                $logContent = self::generateInfoLog($message);
                break;
            case self::WARNING:
                $logContent = self::generateWarningLog($message);
                break;
            case self::ERROR:
                $logContent = self::generateErrorLog($message);
                break;
        }

        self::write($logContent);
    }
}