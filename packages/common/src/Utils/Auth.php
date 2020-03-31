<?php

namespace IndodanaCommon\Utils;

class Auth
{
  public function getBearerCredentials($authToken)
  {
    if (!empty($authToken)) {
      if (preg_match('/Bearer\s(\S+)/', $authToken, $matches)) {
        return $matches[1];
      }
    }

    return null;
  }
}
