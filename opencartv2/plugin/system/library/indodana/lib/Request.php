<?php
class IndodanaRequest
{
  public static function post($url, $data = '', $header = array())
  {
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_HTTPHEADER, $header);
    curl_setopt($request, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($request);
    $error = curl_error($request);

    if ($error) {
      throw new Exception($error);
    }

    curl_close($request);

    return $response;
  }
}