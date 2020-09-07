<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;

class Redirectto extends \Magento\Framework\App\Action\Action
{
   protected $_resultFactory;
   protected $_transaction;
   protected $_request;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\Action\Context $context,
        Transaction $transaction,
        \Magento\Framework\App\Request\Http $request

    )
    {
        $this->_resultFactory = $jsonResultFactory;
        $this->_transaction = $transaction;
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute(){
        
        IndodanaLogger::info('test logger');
        //IndodanaLogger::info('paytype => ' . $postData);
        //$paytype =$this->_request->getParam('paytype'); //$postData('paytype') ;
        $post = $this->_request->getPostValue();
        $paytype=$post['paytype'];
        //IndodanaLogger::info($post['paytype']);
        $result = $this->_resultFactory->create();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        
        //$Installment=$this->_transaction->getInstallmentOptions($cart->getQuote());
        //$bill =  $this->_transaction->getBillingAddress($cart->getQuote());
        //$ship =  $this->_transaction->getShippingAddress($cart->getQuote());
        //$order =  $this->_transaction->getOrderData($cart->getQuote(),$paytype);
        $order =  $this->_transaction->checkOut($cart->getQuote(),$paytype); 
        //$product =$this->_transaction->getProducts($cart->getQuote());
        return $result->setData(
            [
                'success' => true,
                'message' => __('Your message here'),
                //'Installment' => $Installment,
                'Order' =>$order,
                //'ship' =>$ship,
                //'bill'=>$bill
            ]
            );    
    }


}