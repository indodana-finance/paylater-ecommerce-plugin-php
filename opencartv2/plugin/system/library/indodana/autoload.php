<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Required for IndodanaLogger
define('INDODANA_LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

// Initialize Sentry
use IndodanaCommon\IndodanaCommon;

$sentryDsn = IndodanaCommon::getSentryDsn('OPENCARTV2');

$client = new Raven_Client($sentryDsn);
$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();

