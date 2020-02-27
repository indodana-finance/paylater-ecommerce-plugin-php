<?php

class IndodanaApi
{
  const SANDBOX_URL = 'https://sandbox01-api.indodana.com/chermes';
  const PRODUCTION_URL = 'https://api.indodana.com/chermes';

  private $apiKey;
  private $apiSecret;
  private $isProduction;
  
  private $baseUrl;

  public function IndodanaApi($apiKey, $apiSecret, $isProduction = false)
  {
    $this->apiKey = $apiKey;
    $this->apiSecret = $apiSecret;
    $this->isProduction = $isProduction;

    $this->baseUrl = $this->isProduction ? self::PRODUCTION_URL : self::SANDBOX_URL;
  }

  public function getPaymentOptions($amount, $items) {
    $url = $this->baseUrl . '/merchant/v1/payment_calculation';
    $data = array(
      'amount'  => $amount,
      'items'   => $items
    );
    $header = self::createDefaultHeader($this->apiKey, $this->apiSecret);
    
    $json = json_encode($data);
    IndodanaLogger::log(IndodanaLogger::INFO, $json);
    
    $responseJson = IndodanaRequest::post($url, $json, $header);
    $response = json_decode($responseJson, true);

    if ($response['status'] == "OK") {
      IndodanaLogger::log(IndodanaLogger::INFO, json_encode($response));
      return $response['payments'];
    } else {
      IndodanaLogger::log(IndodanaLogger::ERROR, json_encode($response));
      throw new Exception('Could not get installments data');
    }
  }

  public function getBaseUrl() {
    return $this->baseUrl;
  }

  private static function createDefaultHeader($apiKey, $apiSecret)
  {
    $bearer = self::generateBearer($apiKey, $apiSecret);
    $header = array(
      'Content-type: application/json',
      'Accept: application/json',
      'Authorization: Bearer ' . $bearer
    );

    return $header;
  }

  public static function generateBearer($apiKey, $apiSecret)
  {
    $nonce = time();
    $content = $apiKey . ':' . $nonce;
    $signature = self::generateSignature($apiSecret, $content);
    $bearer = $content . ':' . $signature;

    return $bearer;
  }

  private static function generateSignature($apiSecret, $content)
  {
    return hash_hmac('sha256', $content, $apiSecret);
  }
}
