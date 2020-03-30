<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\MerchantResponse;

class Indodana_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{
  public function redirectAction()
  {
    $order = $this->getLatestOrder();

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
      [ 'template' => 'indodanapayment/redirect.phtml' ]
    );

    return $block;
  }

  private function renderBlock($block)
  {
    $this->getLayout()->getBlock('content')->append($block);

    $this->renderLayout();
  }

  /**
   * Get latest order
   *
   * TODO: It's actually preferable to get order based on order id.
   * But we will leave it like this for awhile.
   *
   * @return Order
   */
  private function getLatestOrder()
  {
    // Get latest order id from local session
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

    if ($orderId) {
      $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

      if ($order->getId()) {
        return $order;
      }
    }

    return null;
  }

  public function confirmOrderAction()
  {
    // Disallow any action for invalid request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->norouteAction();
    }

    $namespace = '[MagentoV1-confirmOrderAction]';

    $requestBody = IndodanaHelper::getRequestBody();

    IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($request_body)
      )
    );

    $order = $this->getLatestOrder();

    if ($order) {
      $order
        ->addStatusToHistory(
          Mage::helper('indodanapayment')->getDefaultOrderPendingStatus(),
          'Order has been placed on Indodana'
        )
        ->save();
    }

    MerchantResponse::printSuccessResponse($namespace);

    return;
  }

  public function successAction()
  {
    // Redirect to home page for invalid request
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      return Mage_Core_Controller_Varien_Action::_redirect('');
    }

    Mage::getSingleton('checkout/session')->unsQuoteId();

    Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => false));
  }

  public function cancelAction()
  {
    // Redirect to home page for invalid request
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      return Mage_Core_Controller_Varien_Action::_redirect('');
    }

    $order = $this->getLatestOrder();

    if ($order) {
      $order
        ->addStatusToHistory(
          Mage::helper('indodanapayment')->getDefaultOrderFailedStatus(),
          'Failed to complete order on Indodana'
        )
        ->save();
    }

    // TODO: If possible, redirect to Magento's cancel page instead
    return Mage_Core_Controller_Varien_Action::_redirect('checkout/cart');
  }

  public function notifyAction()
  {
    // Disallow any action for invalid request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->norouteAction();
    }

    // Log request headers
    // -----
    $namespace = '[MagentoV1-notifyAction]';

    $requestHeaders = getallheaders();

    IndodanaLogger::info(
      sprintf(
        '%s Request headers: %s',
        $namespace,
        json_encode($requestHeaders)
      )
    );

    // Check whether request authorization is valid
    // -----
    $authToken = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : '';

    $isValidAuthorization = Mage::helper('indodanapayment/transaction')
      ->getIndodanaService()
      ->isValidAuthToken($authToken);

    if (!$isValidAuthorization) {
      MerchantResponse::printInvalidRequestAuthResponse($namespace);

      return;
    }

    // Log request body
    // -----
    $requestBody = IndodanaHelper::getRequestBody();

    IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($requestBody)
      )
    );

    // Check whether request body is valid
    // -----
    if (!isset($requestBody['transactionStatus']) || !isset($requestBody['merchantOrderId'])) {
      MerchantResponse::printInvalidRequestBodyResponse($namespace);

      return;
    }

    $transactionStatus = $requestBody['transactionStatus'];
    $orderId = $requestBody['merchantOrderId'];

    $order = Mage::getModel('sales/order');
    $order->load($orderId);

    if (!$order) {
      MerchantResponse::printNotFoundOrderResponse(
        $orderId,
        $namespace
      );

      return;
    }

    if (!in_array($transactionStatus, IndodanaConstant::getSuccessTransactionStatuses())) {
      MerchantResponse::printInvalidTransactionStatusResponse(
        $transactionStatus,
        $orderId,
        $namespace
      );

      return;
    }

    // Handle success order
    // -----
    $this->handleSuccessOrder($order);

    MerchantResponse::printSuccessResponse($namespace);

    return;
  }

  private function handleSuccessOrder(&$order) {
    // Save invoice && transaction
    // -----
    $invoice = $order->prepareInvoice()
       ->setTransactionId($order->getId())
       ->addComment('Transaction is successfully processed by Indodana')
       ->register()
       ->pay();

    $transaction = Mage::getModel('core/resource_transaction')
      ->addObject($invoice)
      ->addObject($invoice->getOrder());

    $transaction->save();

    // Set order as success
    // -----
    $order
      ->addStatusToHistory(
        Mage::helper('indodanapayment')->getDefaultOrderSuccessStatus(),
        'Order on Indodana is successfully completed'
      )
      ->save();
  }
}
