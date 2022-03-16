<?php

namespace Indodana\PayLater\Controller\Index;

require_once( dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

use Indodana\PayLater\Helper\Transaction;
use Indodana\PayLater\Helper\Data;
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
  protected $_coretransaction;
  protected $_order;
  
  public function __construct (
    \Magento\Framework\Controller\Result\JsonFactory $pageFactory,        
    \Magento\Framework\App\Action\Context $context,
    \Indodana\PayLater\Helper\Transaction $transaction,
    \Magento\Framework\App\Request\Http $request,
    \Indodana\PayLater\Helper\Data $helper,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Sales\Model\OrderFactory $orderFactory,
    \Magento\Framework\DB\Transaction $coretransaction,
    \Magento\Sales\Api\Data\OrderInterface $order,
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
  )
  {    
    $this->_resultFactory = $pageFactory;
    $this->_transaction = $transaction;
    $this->_request = $request;
    $this->_helper = $helper;
    $this->_checkoutSession = $checkoutSession;
    $this->_orderFactory = $orderFactory;
    $this->_coretransaction=$coretransaction;
    $this->_order=$orderRepository;
    
    return parent::__construct($context);
  }

  public function execute(){
    $namespace = '[MagentoV2-Indodana\PayLater\Controller\Index\Notify\execute]';
    $result = $this->_resultFactory->create();
    $this->notifyAction();
    
    return ;
  }
    
  public function notifyAction(){
    $namespace = '[MagentoV2-Indodana\PayLater\Controller\Index\Notify\notifyAction]';
    //Disallow any action for invalid request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

      return;
    }  
    // Log request headers
    // -----    
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
    $order= $this->_order->get($orderid);  
    
    if (!$order) {
      MerchantResponse::printNotFoundOrderResponse(
        $orderId,
        $namespace
      );
      
      return;
    }

    if (!in_array($transactionStatus, IndodanaConstant::getSuccessTransactionStatuses())) {
      return MerchantResponse::printInvalidTransactionStatusResponse(
        $transactionStatus,
        $orderId,
        $namespace
      );  
    }

    // Handle success order
    // -----
    $this->handleSuccessOrder($order);  
    MerchantResponse::printSuccessResponse($namespace);  
    
    return $result;
  }

  private function handleSuccessOrder($order) {
    // Save invoice && transaction
    // -----
    $invoice = $order->prepareInvoice()
      ->setTransactionId($order->getId())
      ->addComment('Transaction is successfully processed by Indodana')
      ->register()
      ->pay();
    $transactionSave = $this->_coretransaction->addObject($invoice)
      ->addObject($invoice->getOrder());
    $transactionSave->save();  
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
