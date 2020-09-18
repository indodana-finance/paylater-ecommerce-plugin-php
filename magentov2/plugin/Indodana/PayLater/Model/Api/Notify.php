<?php

namespace Indodana\PayLater\Model\Api;

class Notify implements \Indodana\PayLater\Api\NotifyInterface
{
    protected $_order;
    protected $_transaction;
    protected $_helper;
    protected $_dir;

    public function __construct
    (
            \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            \Indodana\PayLater\Helper\Transaction $transaction,
            \Indodana\PayLater\Helper\Data $helper,
            \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    )
    {
            $this->_order=$orderRepository;
            $this->_transaction = $transaction;
            $this->_helper = $helper;
            $this->_dir = $directoryList;
            /// use by indodana logger
            //define('INDODANA_LOG_DIR',$this->_dir->getPath('log'). DIRECTORY_SEPARATOR . 'Indodana' . DIRECTORY_SEPARATOR );

            if (!is_dir(INDODANA_LOG_DIR)) {
              mkdir(INDODANA_LOG_DIR, 0777, true);
            }

    }


    public function OKReturn()
    {
        return [
            'status' => 'OK',
            'message' => 'Message from merchant if any'
        ];
    }
    public function RejectReturn(){
        return [
            'status' => 'REJECT',
            'message' => 'Message from merchant if any'
        ];

    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function Approve()
    {
        $namespace = '[MagentoV2-Indodana\PayLater\Model\Api\Notify\Approve]';
        IndodanaLogger::info(
            sprintf(
              '%s Enter Approve Api ',
              $namespace
            )
          );  
  
        $this->notifyAction();
        
    }

    public function notifyAction(){
        $namespace = '[MagentoV2-Indodana\PayLater\Model\Api\Notify\notifyAction]';
        //Disallow any action for invalid request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
          $this->RejectReturn();
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
          
          $this->RejectReturn();
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
          
          $this->RejectReturn();
        }  
    
        $transactionStatus = $requestBody['transactionStatus'];
        $orderId = str_replace('KK','',$requestBody['merchantOrderId']);      
        $order= $this->_order->get($orderid);  

        IndodanaLogger::info(
            sprintf(
              '%s Order: %s',
              $namespace,
              json_encode($order)
            )
          );  
  
        
        if (!$order) {
          MerchantResponse::printNotFoundOrderResponse(
            $orderId,
            $namespace
          );
          
          $this->RejectReturn();
        }
    
        if (!in_array($transactionStatus, IndodanaConstant::getSuccessTransactionStatuses())) {
          MerchantResponse::printInvalidTransactionStatusResponse(
            $transactionStatus,
            $orderId,
            $namespace
          );  
          
          $this->RejectReturn();
        }
    
        // Handle success order
        // -----
        $this->handleSuccessOrder($order);  
        MerchantResponse::printSuccessResponse($namespace);  
        
        $this->OKReturn();;
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