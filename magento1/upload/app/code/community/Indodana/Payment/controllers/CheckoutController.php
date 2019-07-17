<?php

// require_once Mage::getBaseDir('lib') . '/Indodana/Checkout/autoload.php';

class Indodana_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function redirectAction()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order   = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        var_dump($order);

        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'indodanapayment',
            array('template' => 'indodanapayment/redirect.phtml')
        );
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function indexAction()
    {
        echo "WASHIAPP";
    }
}