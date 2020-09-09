<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;

class sukses extends \Magento\Framework\App\Action\Action
{
   protected $_resultFactory;
   protected $_transaction;
   protected $_request;
    protected $_checkoutSession;
    protected $_orderFactory;
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,        
        \Magento\Framework\App\Action\Context $context
        // Transaction $transaction,
        // \Magento\Framework\App\Request\Http $request,
        // \Magento\Checkout\Model\Session $checkoutSession,
        // \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_resultFactory = $pageFactory;
        // $this->_transaction = $transaction;
        // $this->_request = $request;
        // $this->_checkoutSession = $checkoutSession;
        // $this->_orderFactory = $orderFactory;
        return parent::__construct($context);
    }

    // public function getRealOrderId()
    // {
    //     $lastorderId = $this->_checkoutSession->getLastOrderId();
    //     return $lastorderId;
    // }

    // public function getOrder()
    // {
    //     if ($this->_checkoutSession->getLastRealOrderId()) {
    //          $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
    //     return $order;
    //     }
    //     return false;
    // }

    public function execute(){
        

        $result = $this->_resultFactory->create();
        return $result;
    //    return $result->setData(
    //         [
    //             'success' => true,
    //             'message' => __('Your message here')
    //         ]
    //         );    
    }


}