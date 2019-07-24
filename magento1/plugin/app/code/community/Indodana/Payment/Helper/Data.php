<?php

class Indodana_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getApiSecret()
    {
        return Mage::getStoreConfig('payment/indodanapayment/api_secret');
    }
    
    public function getApiKey()
    {
        return Mage::getStoreConfig('payment/indodanapayment/api_key');
    }
    
    public function getSuccessfulTransactionStatus()
    {
        return Mage::getStoreConfig('payment/indodanapayment/order_status');
    }

    public function getFailedTransactionStatus()
    {
        return Mage::getStoreConfig('payment/indodanapayment/order_status_failed');
    }
}