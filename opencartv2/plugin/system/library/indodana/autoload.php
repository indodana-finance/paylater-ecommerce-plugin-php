<?php

// Starts from Opencart 2.2.0.0, it automatically autoload `admin/`, `catalog/` & `system/`
// Therefore, we need to avoid autoload it twice to ensure the classes are declared only once
// Reference: https://github.com/opencart/opencart/pull/3513
if (version_compare(VERSION, '2.2.0.0', '<')) {
  require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
}

// Required for IndodanaLogger
define('INDODANA_LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);
