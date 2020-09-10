<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;
use Magento\Framework\View\Result\ResultFactory;

class Cancel extends \Magento\Framework\App\Action\Action
{
   protected $_resultFactory;
   protected $_transaction;
   protected $_request;
   protected $_helper;
   protected $_checkoutSession;
   protected $_orderFactory;
    public function __construct(
        \Magento\Framework\Controller\Result\RedirectFactory  $pageFactory,        
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
        

        // Redirect to home page for invalid request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return; //Mage_Core_Controller_Varien_Action::_redirect('');
        }
    
        //$order = $this->getLatestOrder();
        $order = $this->getOrder();
    
        if ($order) {
            $order
            ->addStatusToHistory(
                $this->_helper->getDefaultOrderFailedStatus(),
                'Failed to complete order on Indodana'
            )
            ->save();
        }
    
        // TODO: If possible, redirect to Magento's cancel page instead
        //return Mage_Core_Controller_Varien_Action::_redirect('checkout/cart');
        //$this->goBack();
     }

     protected function goBack()
     {       

        $resultRedirect = $this->_resultFactory->create();
        //$resultRedirect->setPath('checkout/cart');
        //return $resultRedirect;

        $url = 'http://localhost/magentoce240s/checkout/cart';
        $resultRedirect->setUrl($url);
        return $resultRedirect;        
                
     }


}