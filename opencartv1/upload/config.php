<?php
// HTTP
define('HTTP_SERVER', '');
define('HTTP_IMAGE', 'image/');
define('HTTP_ADMIN', 'admin/');

// HTTPS
define('HTTPS_SERVER', '');
define('HTTPS_IMAGE', 'image/');

// Our own custom function
function opencart_upload_path($path) {
    return __DIR__ . $path;
}

// DIR
define('DIR_APPLICATION', opencart_upload_path('/catalog/'));
define('DIR_SYSTEM', opencart_upload_path('/system/'));
define('DIR_DATABASE', opencart_upload_path('/system/database/'));
define('DIR_LANGUAGE', opencart_upload_path('/catalog/language/'));
define('DIR_TEMPLATE', opencart_upload_path('/catalog/view/theme/'));
define('DIR_CONFIG', opencart_upload_path('/system/config/'));
define('DIR_IMAGE', opencart_upload_path('/image/'));
define('DIR_CACHE', opencart_upload_path('/system/cache/'));
define('DIR_DOWNLOAD', opencart_upload_path('/download/'));
define('DIR_LOGS', opencart_upload_path('/system/logs/'));

// DB
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'opencart');
define('DB_PASSWORD', 'opencart');
define('DB_DATABASE', 'opencart');
define('DB_PREFIX', '');
?>
