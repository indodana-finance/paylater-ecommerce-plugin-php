<?php

namespace Indodana\PayLater\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Payment\Gateway\Config as ConfigInterface;

class Data extends AbstractHelper
{
  protected $_scopeConfig;

  public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
  {
    $this->_scopeConfig = $scopeConfig;
  }

  public function getStoreID()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreName()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreUrl()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreEmail()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStorePhone()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_phone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreCountryCode()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_country_code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreCity()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStoreAddress()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_address', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getStorePostalCode()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/store_postal_code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getApiKey()
  {
    if ($this->getEnvironment()=='SANDBOX') {
      return $this->_scopeConfig->getValue('payment/indodanapayment/api_key_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    } else {
      return $this->_scopeConfig->getValue('payment/indodanapayment/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
  }

  public function getApiSecret()
  {
    if ($this->getEnvironment()=='SANDBOX') {
      return $this->_scopeConfig->getValue('payment/indodanapayment/api_secret_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    } else {
      return $this->_scopeConfig->getValue('payment/indodanapayment/api_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
  }

  public function getEnvironment()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getDefaultOrderPendingStatus()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/default_order_pending_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getDefaultOrderSuccessStatus()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/default_order_success_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getDefaultOrderFailedStatus()
  {
    return $this->_scopeConfig->getValue('payment/indodanapayment/default_order_failed_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
}
