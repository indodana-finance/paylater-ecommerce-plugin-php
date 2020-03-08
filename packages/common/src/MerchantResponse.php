<?php

namespace IndodanaCommon;

class MerchantResponse
{
  public static function createSuccessResponse()
  {
    return [
      'status'  => 'OK',
      'message' => 'Payment status updated'
    ];
  }

  public static function createInvalidAuthResponse()
  {
    return [
      'status'  => 'REJECTED',
      'message' => 'Invalid authorization header'
    ];
  }

  public static function createInvalidRequestBodyResponse()
  {
    return [
      'status'  => 'REJECTED',
      'message' => 'Invalid request body'
    ];
  }

  public static function createNotFoundOrderResponse($order_id)
  {
    return [
      'status'  => 'REJECTED',
      'message' => "Order Not found for merchant order id: ${order_id}"
    ];
  }

  public static function createNotFoundOrderStatusResponse($order_id)
  {
    return [
      'status'  => 'REJECTED',
      'message' => "Status not found for merchant order id: ${order_id}"
    ];
  }
}
