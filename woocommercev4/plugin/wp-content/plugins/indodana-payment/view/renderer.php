<?php

class Renderer {
  public static function render($path, $data) {
    extract($data);

    ob_start();
    include($path);
    $var=ob_get_contents();
    ob_end_clean();

    return $var;
  }
}
