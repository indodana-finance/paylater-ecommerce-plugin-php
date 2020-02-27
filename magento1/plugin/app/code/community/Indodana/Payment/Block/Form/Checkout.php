<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

class Indodana_Payment_Block_Form_Checkout extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();

        $cart = Mage::getModel('checkout/cart')->getQuote();

        $paymentOptions = Mage::helper('indodanapayment/transaction')->getInstallmentOptions($cart);

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
}
