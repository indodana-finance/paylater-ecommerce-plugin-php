<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

use IndodanaCommon\IndodanaLogger;

/*
    You might realize that this class has a very similar function or APIs to the one in Helper
    From a first glance, it might be wise to combine them two. 

    But After closer inspection $order object here and $cart object in helper has a slight different set of APIs
    And combining them two would make it messy
 */
class Indodana_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{
  public function notifyAction()
  {
    // Disallow if not POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->norouteAction();

      return;
    }

    $postData = IndodanaHelper::getJsonPost();
    IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

    $orderId = $postData['merchantOrderId'];
    $this->handleApprovedTransaction($orderId);

    $response = array(
      'status'    => 'OK',
      'message'   => 'Payment status updated'
    );

    header('Content-Type: application/json');
    echo json_encode($response);
  }

  private function handleApprovedTransaction($orderId) {
    /* Load order by orderId */
    $order = Mage::getModel('sales/order');

    $order->load($orderId);

    /* Save transaction */
    $invoice = $order->prepareInvoice()
       ->setTransactionId($order->getId())
       ->addComment('Payment successfully processed by Indodana.')
       ->register()
       ->pay();

    $transaction = Mage::getModel('core/resource_transaction')
      ->addObject($invoice)
      ->addObject($invoice->getOrder());

    $transaction->save();

    /* Mark order as success */
    $statusIfSuccess = Mage::helper('indodanapayment')->getSuccessfulTransactionStatus();

    $order->setStatus($statusIfSuccess);

    $order->save();
  }

  public function confirmOrderAction()
  {
    // Disallow if not POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->norouteAction();

      return;
    }

    $postData = IndodanaHelper::getJsonPost();
    IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

    $response = array(
      'success'   => 'OK'
    );

    header('Content-Type: application/json');
    echo json_encode($response);
  }

  public function successAction()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

      // I don't know what's this but I will comment out this line for this action to be working
      // $this->_log($_GET);

      Mage::getSingleton('checkout/session')->unsQuoteId();
      Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => false));

    } else {
      Mage_Core_Controller_Varien_Action::_redirect('');
    }
  }

  public function cancelAction()
  {
    if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
      $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
      if ($order->getId()) {
        $order->cancel()->setState(
          Mage::helper('indodanapayment')->getFailedTransactionStatus(), 
          true, 
          'Indodana has declined the payment.'
        )->save();
      }
    }

    Mage_Core_Controller_Varien_Action::_redirect('');
  }

  public function redirectAction()
  {
    // Get order
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

    $paymentOptions = Mage::helper('indodanapayment/transaction')->getInstallmentOptions($order);

    $orderData = Mage::helper('indodanapayment/transaction')->getOrderData($order);

    $jsonOrderData = json_encode($orderData);
    $indodanaBaseUrl = Mage::helper('indodanapayment/transaction')->getIndodanaService()->getBaseUrl();
    $merchantConfirmPaymentUrl = Mage::getUrl('indodanapayment/checkout/confirmOrder');
    $authorization = Mage::helper('indodanapayment/transaction')->getIndodanaService()->getAuthToken();

    // Magento 1 use JavaScript on indodanapayment/redirect.phtml to perform checkout
    // UI for this action is defined as "block"
    // Therefore, we "feed" the data to the block
    $block = $this->createBlock();

    $block->assign([
      'paymentOptions' => $paymentOptions,
      'orderData' => $jsonOrderData,
      'indodanaBaseUrl' => $indodanaBaseUrl,
      'merchantConfirmPaymentUrl' => $merchantConfirmPaymentUrl,
      'authorization' => $authorization
    ]);

    $this->renderBlock($block);
  }

  private function createBlock()
  {
    $this->loadLayout();

    $block = $this->getLayout()->createBlock(
      'Mage_Core_Block_Template',
      'indodanapayment',
      ['template' => 'indodanapayment/redirect.phtml']
    );

    return $block;
  }

  private function renderBlock($block)
  {
    $this->getLayout()->getBlock('content')->append($block);

    $this->renderLayout();
  }

}
