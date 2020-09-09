<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;

use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\MerchantResponse;

class Notify extends \Magento\Framework\App\Action\Action
{
   protected $_resultFactory;
   protected $_transaction;
   protected $_request;
   protected $_helper;
   protected $_checkoutSession;
   protected $_orderFactory;
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,        
        \Magento\Framework\App\Action\Context $context,
        Transaction $transaction,
        \Magento\Framework\App\Request\Http $request,
        Indodana\PayLater\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_resultFactory = $pageFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;

        return parent::__construct($context);
    }

    public function getRealOrderId()
    {
        $lastorderId = $this->_checkoutSession->getLastOrderId();
        return $lastorderId;
    }

    public function getOrder()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
             $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        return $order;
        }
        return false;
    }


    public function execute(){
        $result = $this->_resultFactory->create();
        //echo
        
      //================================
     //Disallow any action for invalid request
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         return $this->norouteAction();
       }
  
      // Log request headers
      // -----
      $namespace = '[MagentoV1-notifyAction]';
  
      $requestHeaders = IndodanaHelper::getRequestHeaders();
  
      IndodanaLogger::info(
        sprintf(
          '%s Request headers: %s',
          $namespace,
          json_encode($requestHeaders)
        )
      );
  
      // Check whether request authorization is valid
      // -----
      $authToken = IndodanaHelper::getAuthToken($requestHeaders, $namespace);
  
      $isValidAuthorization = $this->_transaction//Mage::helper('indodanapayment/transaction')
        ->getIndodanaCommon()
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
  
      //$order = Mage::getModel('sales/order');
      $realOrderId = $this->getRealOrderId();
      $order= $this->getOrder();
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
  
      //return;
  
      //===================================        
        
        return $result;
    }

    private function handleSuccessOrder(&$order) {
      // Save invoice && transaction
      // -----
      $invoice = $order->prepareInvoice()
         ->setTransactionId($order->getId())
         ->addComment('Transaction is successfully processed by Indodana')
         ->register()
         ->pay();
  
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      //$transaction = Mage::getModel('core/resource_transaction')
      $transaction =  $objectManager->get('core/resource_transaction')      
        ->addObject($invoice)
        ->addObject($invoice->getOrder());
  
      $transaction->save();
  
      // Set order as success
      // -----
      $order
        ->addStatusToHistory(
          $this->_helper->getDefaultOrderSuccessStatus(),
          'Order on Indodana is successfully completed'
        )
        ->save();
    }
  

}