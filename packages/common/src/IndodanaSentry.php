<?php

namespace IndodanaCommon;

use Indodana\Indodana;
use Indodana\IndodanaHttpClient;

class IndodanaSentry
{
  public function getSentryDsn($pluginName)
  {
    $result = IndodanaHttpClient::get(
      Indodana::PRODUCTION_BASE_URL . '/public/v1/merchant-plugin/sentry',
      [],
      [ 'pluginName' => $pluginName ]
    );

    return $result['data']['sentryDsn'];
  }
}
