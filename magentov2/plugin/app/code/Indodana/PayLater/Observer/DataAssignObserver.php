<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Indodana\PayLater\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $paymentInfo = $method->getInfoInstance();

        if ($data->getDataByKey('transaction_result') !== null) {
            $paymentInfo->setAdditionalInformation(
                'transaction_result',
                $data->getDataByKey('transaction_result')
            );
        }
        if ($data->getDataByKey('installment') !== null) {
            $paymentInfo->setAdditionalInformation(
                'installment',
                $data->getDataByKey('installment')
            );
        }
        if ($data->getDataByKey('paytype') !== null) {
            $paymentInfo->setAdditionalInformation(
                'paytype',
                $data->getDataByKey('paytype')
            );
        }

    }
}
