<?php

namespace Indodana\PayLater\Model\Api;

require_once( dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\MerchantResponse;
use Indodana\PayLater\Helper\Transaction;

class Notify implements \Indodana\PayLater\Api\NotifyInterface
{
    protected $_order;
    protected $_transaction;
    protected $_helper;
    protected $_dir;
    protected $_coretransaction;

    public function __construct
    (
      \Magento\Sales\Api\Data\OrderInterface $order,
      \Indodana\PayLater\Helper\Transaction $transaction,
      \Indodana\PayLater\Helper\Data $helper,
      \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
      \Magento\Framework\DB\Transaction $coretransaction
    )
    {
      $this->_order = $order;
      $this->_transaction = $transaction;
      $this->_helper = $helper;
      $this->_dir = $directoryList;
      $this->_coretransaction=$coretransaction;
      /// use by indodana logger
      //define('INDODANA_LOG_DIR',$this->_dir->getPath('log'). DIRECTORY_SEPARATOR . 'Indodana' . DIRECTORY_SEPARATOR );

      if (!is_dir(INDODANA_LOG_DIR)) {
        mkdir(INDODANA_LOG_DIR, 0777, true);
      }
    }

    public function printResponse($response, $namespace)
    {
      $json_encoded_response = json_encode($response);

      IndodanaLogger::info(sprintf(
        '%s Response: %s',
        $namespace,
        $json_encoded_response
      ));

      header('Content-type: application/json');

      print_r($json_encoded_response, false);

      die;
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
        $isValidAuthorization = $this->_transaction
          ->getIndodanaCommon()
          ->isValidAuthToken($authToken);  

        if (!$isValidAuthorization) {
          return $this->printResponse(
            [
              'status'  => 'REJECTED',
              'message' => 'Invalid request authorization'
            ],
            $namespace
          );
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
          return $this->printResponse(
            [
              'status'  => 'REJECTED',
              'message' => 'Invalid request body'
            ],
            $namespace
          );
        }  
    
        $transactionStatus = $requestBody['transactionStatus'];
        $incrementId = str_replace(Transaction::PREVIX_ORDERID,'',$requestBody['merchantOrderId']);      
        $order= $this->_order->loadByIncrementid($incrementId);  

        IndodanaLogger::info(
          sprintf(
            '%s Order Status: %s',
            $namespace,
            $order->getStatus()
          )
        );  

        IndodanaLogger::info(
          sprintf(
            '%s Order: %s',
            $namespace,
            json_encode($order)
          )
        );  
  
        
        if (!$order) {
          return $this->printResponse(
            [
              'status'  => 'REJECTED',
              'message' => "Order not found for merchant order id: ${orderId}"
            ],
            $namespace
          );
        }
    
        if (!in_array($transactionStatus, IndodanaConstant::getSuccessTransactionStatuses())) {
          return $this->printResponse(
            [
              'status'  => 'REJECTED',
              'message' => "Invalid transaction status: ${transactionStatus} for merchant order id: ${orderId}"
            ],
            $namespace
          );
        }
    
        // Handle success order
        // -----
        $this->handleSuccessOrder($order);  

        return $this->printResponse(
          [
            'status'  => 'OK',
            'message' => 'OK'
          ],
          $namespace
        );
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
