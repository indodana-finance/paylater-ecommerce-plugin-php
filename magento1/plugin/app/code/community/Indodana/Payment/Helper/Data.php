<?php

class Indodana_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function getStoreName()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_name');
  }

  public function getStoreUrl()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_url');
  }

  public function getStoreEmail()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_email');
  }

  public function getStorePhone()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_phone');
  }

  public function getStoreCountryCode()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_country_code');
  }

  public function getStoreCity()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_city');
  }

  public function getStoreAddress()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_address');
  }

  public function getStorePostalCode()
  {
    return Mage::getStoreConfig('payment/indodanapayment/store_postal_code');
  }

  public function getApiKey()
  {
    return Mage::getStoreConfig('payment/indodanapayment/api_key');
  }

  public function getApiSecret()
  {
    return Mage::getStoreConfig('payment/indodanapayment/api_secret');
  }

  public function getEnvironment()
  {
    return Mage::getStoreConfig('payment/indodanapayment/environment');
  }

  public function getDefaultOrderPendingStatus()
  {
    return Mage::getStoreConfig('payment/indodanapayment/default_order_pending_status');
  }

  public function getDefaultOrderSuccessStatus()
  {
    return Mage::getStoreConfig('payment/indodanapayment/default_order_success_status');
  }

  public function getDefaultOrderFailedStatus()
  {
    return Mage::getStoreConfig('payment/indodanapayment/default_order_failed_status');
  }
}
