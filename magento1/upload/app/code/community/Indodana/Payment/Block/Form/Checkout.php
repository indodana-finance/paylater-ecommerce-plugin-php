<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

class Indodana_Payment_Block_Form_Checkout extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->initializeIndodanaApi();

        $cart = Mage::getModel('checkout/cart')->getQuote();

        $transactionObjects = Mage::helper('indodanapayment/transaction')->getTransactionObjects($cart);
        $totalPrice = Mage::helper('indodanapayment/transaction')->calculateTotalPrice($transactionObjects);

        $paymentOptions = $this->indodanaApi->getPaymentOptions($totalPrice, $transactionObjects);
        $lowestMonthlyInstallment = $this->getLowestMonthlyInstallment($paymentOptions);
        $offer = $this->generateOfferMessage($lowestMonthlyInstallment);

        $this->assign('offer', $offer);
        $this->setTemplate('indodanapayment/form/checkout.phtml');
    }

    private function getLowestMonthlyInstallment($paymentOptions)
    {
        return $paymentOptions[count($paymentOptions) - 1]['monthlyInstallment'];
    }

    private function generateOfferMessage($lowestMonthlyInstallment)
    {
        $formattedAmount = Mage::helper('core')->currency($lowestMonthlyInstallment, true, false);
        return "Mulai dari " . $formattedAmount . "/bulan";
    }

    private function initializeIndodanaApi()
    {
        $apiKey = Mage::helper('indodanapayment')->getApiKey();
        $apiSecret = Mage::helper('indodanapayment')->getApiSecret();

        $this->indodanaApi = new IndodanaApi($apiKey, $apiSecret);
    }
}