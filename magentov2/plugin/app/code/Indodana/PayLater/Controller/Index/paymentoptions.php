<?php

namespace Indodana\PayLater\Controller\Index;

require_once( dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

use Exception;
use Indodana\PayLater\Helper\Transaction;

class paymentoptions extends \Magento\Framework\App\Action\Action
{
    protected $_resultFactory;
    protected $_transaction;
    protected $_request;
    
    public function __construct
    (
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
        $errMsg='Nilai transaksi Anda tidak sesuai dengan ketentuan penggunaan Indodana Paylater';
        $isError = false;
        $Installment=[];
        try{
            $Installment=$this->_transaction->getInstallmentOptions($cart->getQuote());
        }catch (Exception $e) {
            $isError=true;
            $errMsg= 'Caught exception: '.  $e->getMessage() . "\n";
        }
        
        $totalOrder = $this->_transaction->getTotalAmount($cart->getQuote());
        $passMinAmount = $this->_transaction->getMinimumTotalAmount() < $totalOrder;
        $products = $this->_transaction->getProducts($cart->getQuote());
        $passMaxPrice = $totalOrder <= 25000000;

        return $result->setData(
            [
                'Installment' => $Installment,
                'OrderID' => $cart->getQuote()->getId(),
                'CurCode' => $this->_transaction->getOrderCurrencyCode($cart->getQuote()),
                'PassMinAmount' => $passMinAmount ,
                'PassMaxItemPrice' => $passMaxPrice ,
                'IsError' => $isError,
                'ErrMsg' => $errMsg
            ]
            );    
    }
}