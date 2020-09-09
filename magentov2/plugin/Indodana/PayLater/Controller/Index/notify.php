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

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,        
        \Magento\Framework\App\Action\Context $context,
        Transaction $transaction,
        \Magento\Framework\App\Request\Http $request

    )
    {
        $this->_resultFactory = $pageFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute(){
        $id = $this->_request->getParam('id');
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
  
//===================================        
        
        return $result->setData(
            [
                'success' => true,
                'message' => __('Your message here')
            ]
            );    
    }


}