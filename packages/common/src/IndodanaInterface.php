<?php

namespace IndodanaCommon;

interface IndodanaInterface
{
  // Transaction
  // Could be cart or order. But for communication purposes, let's use order

  public function getTotalAmount($order);

  public function getTotalDiscountAmount($order);

  public function getTotalShippingAmount($order);

  public function getTotalTaxAmount($order);

  public function getItems($order);

  // Others

  public function getCustomerDetails($order);

  public function getBillingAddress($order);

  public function getShippingAddress($order);

  public function getSeller();
}
