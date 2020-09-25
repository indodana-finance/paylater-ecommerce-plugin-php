<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Indodana\PayLater\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Indodana\PayLater\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'indodanapayment';
    protected $_assetRepo;
    public function __construct(

        \Magento\Framework\View\Asset\Repository $assetRepo
    )
    {
        $this->_assetRepo = $assetRepo;
    }    
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ],
                    'installment'=>'',
                    'paytype'=>'',
                    'logo' =>$this->_assetRepo->getUrl("Indodana_PayLater::images/Indodana-color.png")

                ]
            ]
        ];
    }
}
