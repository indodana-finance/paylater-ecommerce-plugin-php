<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

define('INDODANA_PLUGIN_ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);

// Required for IndodanaLogger
// Logs live in the uploads directory (not under the plugin dir, which is web-reachable and wiped on update)
// and the directory is guarded against direct access.
$indodana_upload_dir = wp_upload_dir();
$indodana_log_dir = $indodana_upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'indodana-logs' . DIRECTORY_SEPARATOR;

if (!is_dir($indodana_log_dir)) {
  wp_mkdir_p($indodana_log_dir);
  file_put_contents($indodana_log_dir . '.htaccess', "Require all denied\nDeny from all\n");
  file_put_contents($indodana_log_dir . 'index.php', "<?php\n// Silence is golden.\n");
}

define('INDODANA_LOG_DIR', $indodana_log_dir);

unset($indodana_upload_dir, $indodana_log_dir);
