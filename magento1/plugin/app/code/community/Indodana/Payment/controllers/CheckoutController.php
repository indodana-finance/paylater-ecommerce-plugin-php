<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

/*
    You might realize that this class has a very similar function or APIs to the one in Helper
    From a first glance, it might be wise to combine them two. 
    
    But After closer inspection $order object here and $cart object in helper has a slight different set of APIs
    And combining them two would make it messy
*/
class Indodana_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function notifyAction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->norouteAction();
            return;
        }

        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $orderId = $postData['merchantOrderId'];
        $this->handleApprovedTransaction($orderId);

        $response = array(
            'status'    => 'OK',
            'message'   => 'Payment status updated'
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function handleApprovedTransaction($orderId) {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);

        $invoice = $order->prepareInvoice()
            ->setTransactionId($order->getId())
            ->addComment('Payment successfully processed by Indodana.')
            ->register()
            ->pay();

        $transaction = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transaction->save();

        $order->setStatus(Mage::helper('indodanapayment')->getSuccessfulTransactionStatus());
        $order->save();
    }

    public function confirmOrderAction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->norouteAction();
            return;
        }

        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $response = array(
            'success'   => 'OK'
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function successAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $this->_log($_GET);
            Mage::getSingleton('checkout/session')->unsQuoteId();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => false));

        } else {
            Mage_Core_Controller_Varien_Action::_redirect('');
        }
    }

    public function cancelAction()
    {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->setState(
                    Mage::helper('indodanapayment')->getFailedTransactionStatus(), 
                    true, 
                    'Indodana has declined the payment.'
                )->save();
            }
        }

        Mage_Core_Controller_Varien_Action::_redirect('');
    }

    public function redirectAction()
    {
        $apiKey = Mage::helper('indodanapayment')->getApiKey();
        $apiSecret = Mage::helper('indodanapayment')->getApiSecret();
        $this->indodanaApi = new IndodanaApi($apiKey, $apiSecret);

        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $itemObjects = $this->getItemObjects($order);

        $totalPrice = $this->calculateTotalPrice($itemObjects);

        $paymentOptions = $this->indodanaApi->getPaymentOptions($totalPrice, $itemObjects);

        $orderData = $this->generateOrderData($order, $itemObjects, $totalPrice);
        $json = json_encode($orderData);

        $block = $this->createBlock();
        $this->loadPaymentOptions($paymentOptions, $block);
        $this->loadOrderDataJson($json, $block);
        $this->loadContextUrl($block);
        $this->loadAuthorization(
            IndodanaApi::generateBearer($apiKey, $apiSecret), 
            $block
        );
        $this->renderBlock($block);
    }

    private function renderBlock($block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    private function loadPaymentOptions($paymentOptions, $block)
    {
        $block->assign(array(
            'paymentOptions'    => $paymentOptions
        ));
    }

    private function loadOrderDataJson($orderData, $block)
    {
        $block->assign(array(
            'orderData' => $orderData
        ));
    }

    private function loadAuthorization($bearer, $block)
    {
        $block->assign(array(
            'authorization'    => 'Bearer ' . $bearer
        ));
    }

    private function loadContextUrl($block)
    {
        $indodanaBaseUrl = $this->indodanaApi->getBaseUrl();
        $merchantConfirmPaymentUrl = Mage::getUrl('indodanapayment/checkout/confirmOrder');
        $block->assign(array(
            'indodanaBaseUrl'           => $indodanaBaseUrl,
            'merchantConfirmPaymentUrl' => $merchantConfirmPaymentUrl
        ));
    }

    private function createBlock()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'indodanapayment',
            array('template' => 'indodanapayment/redirect.phtml')
        );
        return $block;
    }

    private function generateOrderData($order, $itemObjects, $totalPrice)
    {
        $transactionObject = $this->getTransactionObject($order->getId(), $itemObjects, $totalPrice);
        $customerObject = $this->getCustomerObject($order);
        $billingObject = $this->getBillingObject($order);
        $shippingObject = $this->getShippingObject($order);

        $approvedNotificationUrl = Mage::getUrl('indodanapayment/checkout/notify');
        $cancellationRedirectUrl = Mage::getUrl('indodanapayment/checkout/cancel');
        $backToStoreUrl = Mage::getUrl('indodanapayment/checkout/success');

        $orderData = array(
            'transactionDetails'        => $transactionObject,
            'customerDetails'           => $customerObject,
            'billingAddress'            => $billingObject,
            'shippingAddress'           => $shippingObject,
            'approvedNotificationUrl'   => $approvedNotificationUrl,
            'cancellationRedirectUrl'   => $cancellationRedirectUrl,
            'backToStoreUrl'            => $backToStoreUrl
        );

        return $orderData;
    }

    private function getTransactionObject($orderId, $itemObjects, $totalPrice)
    {
        $transactionObject = array(
            'merchantOrderId'   => $orderId,
            'amount'            => $totalPrice,
            'items'             => $itemObjects
        );
        return $transactionObject;
    }

    private function getBillingObject($order)
    {
        $billingAddress  = $order->getBillingAddress();
        return array(
            'firstName'     => $billingAddress->getFirstname(),
            'lastName'      => $billingAddress->getLastname(),
            'address'       => $billingAddress->getStreet(1),
            'city'          => $billingAddress->getCity(),
            'postalCode'    => $billingAddress->getPostcode(),
            'phone'         => $billingAddress->getTelephone(),
            'countryCode'   => $this->countryCode($billingAddress->getCountry()),
        );
    }

    private function getShippingObject($order)
    {
        $shippingAddress  = $order->getShippingAddress();
        return array(
            'firstName'     => $shippingAddress->getFirstname(),
            'lastName'      => $shippingAddress->getLastname(),
            'address'       => $shippingAddress->getStreet(1),
            'city'          => $shippingAddress->getCity(),
            'postalCode'    => $shippingAddress->getPostcode(),
            'phone'         => $shippingAddress->getTelephone(),
            'countryCode'   => $this->countryCode($shippingAddress->getCountry()),
        );
    }

    private function getCustomerObject($order)
    {
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $billingAddress  = $order->getBillingAddress();
        if (!$customer->getId()) {
            $customer = $order->getBillingAddress();
        }
        return array(
            'firstName' => $customer->getFirstname(),
            'lastName'  => $customer->getLastname(),
            'email'     => $customer->getEmail(),
            'phone'     => $billingAddress->getTelephone(),
        );
    }

    private function getItemObjects($order)
    {
        $productObjects = $this->getProductObjects($order);
        $taxObject = $this->getTaxObject($order);
        $shippingCostObject = $this->getShippingCostObject($order);

        $itemObjects = array();
        $itemObjects = array_merge($itemObjects, $productObjects);
        $itemObjects[] = $taxObject;

        if ($shippingCostObject != null) {
            $itemObjects[] = $shippingCostObject;
        }

        return $itemObjects;
    }

    private function getProductObjects($order)
    {
        $productObjects = array();
        foreach($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $productObject = array(
                'id'        => $product->getId(),
                'url'       => $product->getProductUrl(),
                'name'      => $product->getName(),
                'price'     => ceil($product->getPrice()),
                'type'      => '',
                'quantity'  => $item->getQtyToInvoice()
            );
            array_push($productObjects, $productObject);
        }

        return $productObjects;
    }

    private function getTaxObject($order)
    {
        $taxAmount = $order->getTaxAmount();
        $taxObject = array(
            'id'        => 'taxfee',
            'url'       => '',
            'name'      => 'Tax Fee',
            'price'     => ceil($taxAmount),
            'type'      => '',
            'quantity'  => 1
        );

        return $taxObject;
    }

    private function getShippingCostObject($order)
    {
        if (is_bool($order->getShippingAddress())) {
            return null;
        }

        $shippingAmount = $order->getShippingAmount();
        $shippingObject = array(
            'id'        => 'shippingfee',
            'url'       => '',
            'name'      => 'Shipping Fee',
            'price'     => ceil($shippingAmount),
            'type'      => '',
            'quantity'  => 1
        );
        
        return $shippingObject;
    }

    private function calculateTotalPrice($itemObjects)
    {
        $total = 0;
        foreach($itemObjects as $transactionObject) {
            $total += $transactionObject['price'] * $transactionObject['quantity'];
        }

        return $total;
    }

    private function countryCode($country_code)
    {
        // 3 digits country codes
        $cc_three = array(
            'AF' => 'AFG',
            'AX' => 'ALA',
            'AL' => 'ALB',
            'DZ' => 'DZA',
            'AD' => 'AND',
            'AO' => 'AGO',
            'AI' => 'AIA',
            'AQ' => 'ATA',
            'AG' => 'ATG',
            'AR' => 'ARG',
            'AM' => 'ARM',
            'AW' => 'ABW',
            'AU' => 'AUS',
            'AT' => 'AUT',
            'AZ' => 'AZE',
            'BS' => 'BHS',
            'BH' => 'BHR',
            'BD' => 'BGD',
            'BB' => 'BRB',
            'BY' => 'BLR',
            'BE' => 'BEL',
            'PW' => 'PLW',
            'BZ' => 'BLZ',
            'BJ' => 'BEN',
            'BM' => 'BMU',
            'BT' => 'BTN',
            'BO' => 'BOL',
            'BQ' => 'BES',
            'BA' => 'BIH',
            'BW' => 'BWA',
            'BV' => 'BVT',
            'BR' => 'BRA',
            'IO' => 'IOT',
            'VG' => 'VGB',
            'BN' => 'BRN',
            'BG' => 'BGR',
            'BF' => 'BFA',
            'BI' => 'BDI',
            'KH' => 'KHM',
            'CM' => 'CMR',
            'CA' => 'CAN',
            'CV' => 'CPV',
            'KY' => 'CYM',
            'CF' => 'CAF',
            'TD' => 'TCD',
            'CL' => 'CHL',
            'CN' => 'CHN',
            'CX' => 'CXR',
            'CC' => 'CCK',
            'CO' => 'COL',
            'KM' => 'COM',
            'CG' => 'COG',
            'CD' => 'COD',
            'CK' => 'COK',
            'CR' => 'CRI',
            'HR' => 'HRV',
            'CU' => 'CUB',
            'CW' => 'CUW',
            'CY' => 'CYP',
            'CZ' => 'CZE',
            'DK' => 'DNK',
            'DJ' => 'DJI',
            'DM' => 'DMA',
            'DO' => 'DOM',
            'EC' => 'ECU',
            'EG' => 'EGY',
            'SV' => 'SLV',
            'GQ' => 'GNQ',
            'ER' => 'ERI',
            'EE' => 'EST',
            'ET' => 'ETH',
            'FK' => 'FLK',
            'FO' => 'FRO',
            'FJ' => 'FJI',
            'FI' => 'FIN',
            'FR' => 'FRA',
            'GF' => 'GUF',
            'PF' => 'PYF',
            'TF' => 'ATF',
            'GA' => 'GAB',
            'GM' => 'GMB',
            'GE' => 'GEO',
            'DE' => 'DEU',
            'GH' => 'GHA',
            'GI' => 'GIB',
            'GR' => 'GRC',
            'GL' => 'GRL',
            'GD' => 'GRD',
            'GP' => 'GLP',
            'GT' => 'GTM',
            'GG' => 'GGY',
            'GN' => 'GIN',
            'GW' => 'GNB',
            'GY' => 'GUY',
            'HT' => 'HTI',
            'HM' => 'HMD',
            'HN' => 'HND',
            'HK' => 'HKG',
            'HU' => 'HUN',
            'IS' => 'ISL',
            'IN' => 'IND',
            'ID' => 'IDN',
            'IR' => 'RIN',
            'IQ' => 'IRQ',
            'IE' => 'IRL',
            'IM' => 'IMN',
            'IL' => 'ISR',
            'IT' => 'ITA',
            'CI' => 'CIV',
            'JM' => 'JAM',
            'JP' => 'JPN',
            'JE' => 'JEY',
            'JO' => 'JOR',
            'KZ' => 'KAZ',
            'KE' => 'KEN',
            'KI' => 'KIR',
            'KW' => 'KWT',
            'KG' => 'KGZ',
            'LA' => 'LAO',
            'LV' => 'LVA',
            'LB' => 'LBN',
            'LS' => 'LSO',
            'LR' => 'LBR',
            'LY' => 'LBY',
            'LI' => 'LIE',
            'LT' => 'LTU',
            'LU' => 'LUX',
            'MO' => 'MAC',
            'MK' => 'MKD',
            'MG' => 'MDG',
            'MW' => 'MWI',
            'MY' => 'MYS',
            'MV' => 'MDV',
            'ML' => 'MLI',
            'MT' => 'MLT',
            'MH' => 'MHL',
            'MQ' => 'MTQ',
            'MR' => 'MRT',
            'MU' => 'MUS',
            'YT' => 'MYT',
            'MX' => 'MEX',
            'FM' => 'FSM',
            'MD' => 'MDA',
            'MC' => 'MCO',
            'MN' => 'MNG',
            'ME' => 'MNE',
            'MS' => 'MSR',
            'MA' => 'MAR',
            'MZ' => 'MOZ',
            'MM' => 'MMR',
            'NA' => 'NAM',
            'NR' => 'NRU',
            'NP' => 'NPL',
            'NL' => 'NLD',
            'AN' => 'ANT',
            'NC' => 'NCL',
            'NZ' => 'NZL',
            'NI' => 'NIC',
            'NE' => 'NER',
            'NG' => 'NGA',
            'NU' => 'NIU',
            'NF' => 'NFK',
            'KP' => 'MNP',
            'NO' => 'NOR',
            'OM' => 'OMN',
            'PK' => 'PAK',
            'PS' => 'PSE',
            'PA' => 'PAN',
            'PG' => 'PNG',
            'PY' => 'PRY',
            'PE' => 'PER',
            'PH' => 'PHL',
            'PN' => 'PCN',
            'PL' => 'POL',
            'PT' => 'PRT',
            'QA' => 'QAT',
            'RE' => 'REU',
            'RO' => 'SHN',
            'RU' => 'RUS',
            'RW' => 'EWA',
            'BL' => 'BLM',
            'SH' => 'SHN',
            'KN' => 'KNA',
            'LC' => 'LCA',
            'MF' => 'MAF',
            'SX' => 'SXM',
            'PM' => 'SPM',
            'VC' => 'VCT',
            'SM' => 'SMR',
            'ST' => 'STP',
            'SA' => 'SAU',
            'SN' => 'SEN',
            'RS' => 'SRB',
            'SC' => 'SYC',
            'SL' => 'SLE',
            'SG' => 'SGP',
            'SK' => 'SVK',
            'SI' => 'SVN',
            'SB' => 'SLB',
            'SO' => 'SOM',
            'ZA' => 'ZAF',
            'GS' => 'SGS',
            'KR' => 'KOR',
            'SS' => 'SSD',
            'ES' => 'ESP',
            'LK' => 'LKA',
            'SD' => 'SDN',
            'SR' => 'SUR',
            'SJ' => 'SJM',
            'SZ' => 'SWZ',
            'SE' => 'SWE',
            'CH' => 'CHE',
            'SY' => 'SYR',
            'TW' => 'TWN',
            'TJ' => 'TJK',
            'TZ' => 'TZA',
            'TH' => 'THA',
            'TL' => 'TLS',
            'TG' => 'TGO',
            'TK' => 'TKL',
            'TO' => 'TON',
            'TT' => 'TTO',
            'TN' => 'TUN',
            'TR' => 'TUR',
            'TM' => 'TKM',
            'TC' => 'TCA',
            'TV' => 'TUV',
            'UG' => 'UGA',
            'UA' => 'UKR',
            'AE' => 'ARE',
            'GB' => 'GBR',
            'US' => 'USA',
            'UY' => 'URY',
            'UZ' => 'UZB',
            'VU' => 'VUT',
            'VA' => 'VAT',
            'VE' => 'VEN',
            'VN' => 'VNM',
            'WF' => 'WLF',
            'EH' => 'ESH',
            'WS' => 'WSM',
            'YE' => 'YEM',
            'ZM' => 'ZMB',
            'ZW' => 'ZWE',
        );
        // Check if country code exists
        if (isset($cc_three[$country_code]) && $cc_three[$country_code] != '') {
            $country_code = $cc_three[$country_code];
        }
        return $country_code;
    }
}