<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Indodana\PayLater\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class PaymentAction
 */
class Environment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => __('SANDBOX'),
                'label' => __('SANDBOX')
            ],
            [
                'value' => __('PRODUCTION'),
                'label' => __('PRODUCTION')
            ]
        ];
    }
}
