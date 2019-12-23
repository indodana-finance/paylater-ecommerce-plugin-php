<?php
// HTTP
define('HTTP_SERVER', 'http://localhost:8001/admin/');
define('HTTP_CATALOG', 'http://localhost:8001/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost:8001/admin/');
define('HTTPS_CATALOG', 'http://localhost:8001/');

define('DEV_PROJECT_DIR', __DIR__ . '/../');

// DIR
define('DIR_APPLICATION', DEV_PROJECT_DIR . '/admin/');
define('DIR_SYSTEM', DEV_PROJECT_DIR . '/system/');
define('DIR_DATABASE', DEV_PROJECT_DIR . '/system/database/');
define('DIR_LANGUAGE', DEV_PROJECT_DIR . '/admin/language/');
define('DIR_TEMPLATE', DEV_PROJECT_DIR . '/admin/view/template/');
define('DIR_CONFIG', DEV_PROJECT_DIR . '/system/config/');
define('DIR_IMAGE', DEV_PROJECT_DIR . '/image/');
define('DIR_CACHE', DEV_PROJECT_DIR . '/system/cache/');
define('DIR_DOWNLOAD', DEV_PROJECT_DIR . '/download/');
define('DIR_LOGS', DEV_PROJECT_DIR . '/system/logs/');
define('DIR_CATALOG', DEV_PROJECT_DIR . '/catalog/');

// DB
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'opencartv1');
define('DB_PREFIX', 'oc_');
?>
