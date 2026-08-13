<?php

namespace Indodana\PayLater\Controller\Index;

require_once( dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

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
    protected $_orderRepository;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\Action\Context $context,
        \Indodana\PayLater\Helper\Transaction $transaction,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Indodana\PayLater\Helper\Data $helper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->_resultFactory = $jsonResultFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        $this->_orderRepository = $orderRepository;
        
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

    public function execute()
    {   $namespace = '[Magentov2 - Indodana\PayLater\Controller\Index\Redirectto\execute]';            
        $post = $this->_request->getPostValue();
        $paytype=$post['paytype'];
        IndodanaLogger::info(
            sprintf(
              '%s OrderId: %s',
              $namespace,
              $this->getRealOrderId()
            )
          );            

        $order= $this->getOrder();     
        IndodanaLogger::info(
            sprintf(
              '%s $order.getId() : %s',
              $namespace,
              $order->getId()
            )
          );            

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

        $checkout =  $this->_transaction->checkOut($order,$paytype);

        $result = $this->_resultFactory->create();
        return $result->setData(
            [
                'success' => true,
                'Order' => $checkout
            ]
        );
    }
}