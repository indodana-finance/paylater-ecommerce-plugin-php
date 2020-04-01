<?php

namespace IndodanaCommon;

use IndodanaCommon\Exceptions\IndodanaCommonException;
use Indodana\Indodana;
use Indodana\IndodanaHttpClient;

class IndodanaSentry
{
  public function getSentryDsn($pluginName)
  {
    $response = IndodanaHttpClient::get(
      Indodana::PRODUCTION_BASE_URL . '/public/v1/merchant-plugin/sentry',
      [],
      [ 'pluginName' => $pluginName ]
    );

    if (!isset($response)) {
      throw new IndodanaCommonException('No response from Indodana server');
    }

    if (!isset($response['data'])) {
      throw new IndodanaCommonException('Response key "data" is not supplied');
    }

    $data = $response['data'];

    if (!isset($data['sentryDsn'])) {
      throw new IndodanaCommonException('Response data with key "sentryDsn" is not supplied');
    }

    return $data['sentryDsn'];
  }
}
