<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
define('INDODANA_PLUGIN_ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('INDODANA_LIB_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
define('INDODANA_LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

// Initialize Sentry
use IndodanaCommon\IndodanaService;

$sentryDsn = IndodanaService::getSentryDsn('WOOCOMMERCE');

Sentry\init(['dsn' => $sentryDsn ]);

spl_autoload_register(function($className) {
    static $classMap;

    if (!isset($classMap)) {
        $classMap = require __DIR__ . DIRECTORY_SEPARATOR . 'classmap.php';
    }

    if (isset($classMap[$className])) {
        include $classMap[$className];
    }
});
