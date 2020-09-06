<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Indodana\Paylater\Model\Adminhtml\Source;

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
                'value' => __('SANBOX'),
                'label' => __('SANBOX')
            ],
            [
                'value' => __('PRODUCTION'),
                'label' => __('PRODUCTION')
            ]


        ];
    }
}
