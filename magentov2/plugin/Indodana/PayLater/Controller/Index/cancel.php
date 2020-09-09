<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;


class Cancel extends \Magento\Framework\App\Action\Action
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
     // Redirect to home page for invalid request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return Mage_Core_Controller_Varien_Action::_redirect('');
        }
    
        //$order = $this->getLatestOrder();
        $order = $this->getOrder();
    
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


}