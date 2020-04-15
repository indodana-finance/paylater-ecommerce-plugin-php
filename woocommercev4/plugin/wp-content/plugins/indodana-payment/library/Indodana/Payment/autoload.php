<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

define('INDODANA_PLUGIN_ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);

// Required for IndodanaLogger
define('INDODANA_LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

// Initialize Sentry
use IndodanaCommon\IndodanaCommon;

$sentryDsn = IndodanaCommon::getSentryDsn('WOOCOMMERCEV4');

Sentry\init(['dsn' => $sentryDsn ]);
