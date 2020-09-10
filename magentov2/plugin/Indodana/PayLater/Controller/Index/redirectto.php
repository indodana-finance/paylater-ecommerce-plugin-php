<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;
use Indodana\PayLater\Helper\Data;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;

class Redirectto extends \Magento\Framework\App\Action\Action
{
   protected $_resultFactory;
   protected $_transaction;
   protected $_request;
    protected $_checkoutSession;
    protected $_orderFactory;
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\Action\Context $context,
        \Indodana\PayLater\Helper\Transaction $transaction,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Indodana\PayLater\Helper\Data $helper

    )
    {
        $this->_resultFactory = $jsonResultFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
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
        
        $post = $this->_request->getPostValue();

        $paytype=$post['paytype'];

        $result = $this->_resultFactory->create();

        $order= $this->getOrder();

        $checkout =  $this->_transaction->checkOut($order,$paytype); 
        $namespace = '[Indodana\PayLater\Controller\Index]';
        if ($order) {        
            IndodanaLogger::info(
                sprintf(
                  '%s DefaultOrderPendingStatus: %s',
                  $namespace,
                  $this->_helper->getDefaultOrderPendingStatus()
                )
              );
            
            $order
              ->addStatusToHistory(
                $this->_helper->getDefaultOrderPendingStatus(),
                'Order has been placed on Indodana'
              )
              ->save();
        }

        return $result->setData(
            [
                'success' => true,
                'message' => __('Your message here'),
                'Order' =>$checkout
            ]
            );    
    }


}