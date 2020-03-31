<?php

namespace IndodanaCommon;

interface IndodanaInterface
{
  // For transactionDetails payload. Could be from cart or order
  // ------------------------------
  public function getTotalAmount($order);

  public function getTotalDiscountAmount($order);

  public function getTotalShippingAmount($order);

  public function getTotalTaxAmount($order);

  public function getProducts($order);

  // Others
  // ------------------------------
  public function getCustomerDetails($order);

  public function getBillingAddress($order);

  public function getShippingAddress($order);

  public function getSeller();
}
