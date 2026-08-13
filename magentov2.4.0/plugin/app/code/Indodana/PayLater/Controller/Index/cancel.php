<?php

namespace Indodana\PayLater\Controller\Index;

require_once( dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

use Indodana\PayLater\Helper\Transaction;

class Cancel extends \Magento\Framework\App\Action\Action
{
    protected $_resultFactory;
    protected $_transaction;
    protected $_request;
    protected $_helper;
    protected $_checkoutSession;
    protected $_orderFactory;

    public function __construct(        
        \Magento\Framework\Controller\Result\RedirectFactory $pageFactory,        
        \Magento\Framework\App\Action\Context $context,
        \Indodana\PayLater\Helper\Transaction $transaction,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Indodana\PayLater\Helper\Data $helper
    )
    {
        $this->_resultFactory = $pageFactory; 
        $this->_transaction = $transaction;
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper=$helper;
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
        $namespace = '[MagentoV2-Indodana\PayLater\Controller\Index\cancel]';
        // Redirect to home page for invalid request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {            
            return; 
        }

        try {
            $order = $this->getOrder();        
            if ($order) {
                $order
                ->addStatusToHistory(
                    $this->_helper->getDefaultOrderFailedStatus(),
                    'Failed to complete order on Indodana'
                )
                ->save();
            }
        } catch (Exception $e) {
            IndodanaLogger::error(
            sprintf(
                '%s Error Msg : %s',
                $namespace,
                $e->getMessage()
            )
            );
        }
                
        $resultRedirect=$this->_resultFactory->create();
        $resultRedirect->setPath('checkout/cart');

        return $resultRedirect;
     }
}