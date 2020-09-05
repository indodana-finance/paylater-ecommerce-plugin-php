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
        return parent::__construct($context);
    }

    public function execute(){
        $id = $this->request->getParam('id');
        $result = $this->_resultFactory->create();

        return $result->setData(
            [
                'success' => true,
                'message' => __('Your message here')
            ]
            );    
    }


}