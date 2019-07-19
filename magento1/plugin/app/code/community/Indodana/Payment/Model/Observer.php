<?php

class Indodana_Payment_Model_Observer
{
    public function disable(Varien_Event_Observer $observer)
    {
        $moduleName = "Indodana_Payment";
        if ('indodanapayment' == $observer->getMethodInstance()->getCode()) {
            if (Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName)) {
                $observer->getResult()->isAvailable = false;
            }
        }
    }
}