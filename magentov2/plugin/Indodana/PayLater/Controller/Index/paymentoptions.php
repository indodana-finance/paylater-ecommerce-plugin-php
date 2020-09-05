<?php

namespace Indodana\PayLater\Controller\Index;

use Indodana\PayLater\Helper\Transaction;


class Paymentoptions extends \Magento\Framework\App\Action\Action
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
        $result = $this->_resultFactory->create();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        
        $Installment=$this->_transaction->getInstallmentOptions($cart->getQuote());
        return $result->setData(
            [
                'success' => true,
                'message' => __('Your message here'),
                'Installment' => $Installment,
            ]
            );    
    }


}