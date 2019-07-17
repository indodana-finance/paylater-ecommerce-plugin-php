<?php

class Indodana_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'indodanapayment';
    protected $_formBlockType = 'indodanapayment/form_checkout';
    // protected $_infoBlockType = 'indodanapayment/info/checkout';

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('indodanapayment/checkout/redirect', array('_secure' => true));
    }
}