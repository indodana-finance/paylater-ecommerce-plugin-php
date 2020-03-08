<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Required for IndodanaLogger
define('INDODANA_LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

// Initialize Sentry
use IndodanaCommon\IndodanaService;

$sentryDsn = IndodanaService::getSentryDsn('OPENCARTV2');

// $client = new Raven_Client($sentryDsn);
// $client = new Raven_Client('http://9655054342d54526b48e39039661640e@sentry.cermati.com/29');
$client = new Raven_Client('https://b27e1f1ae987411cbfeb9688db2802a3@sentry.io/4041300');
$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();

