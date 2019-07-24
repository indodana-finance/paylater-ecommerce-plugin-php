<?php

class WC_Indodana_Gateway extends WC_Payment_Gateway
{
    private $indodanaApi;

    public function __construct()
    {
        $this->id = 'indodana';
        $this->icon = '';
        $this->hasFields = true;

        $this->method_title = __('Indodana Payment', 'indodana-method-title');
        $this->method_description = __('Payment', 'indodana-method-description');

        $this->supports = array(
            'products'
        );

        $this->init_form_fields();

        $this->init_settings();
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->isProduction = $this->get_option('environment') === 'production';
        $this->apiKey = $this->get_option('api_key');
        $this->apiSecret = $this->get_option('api_secret');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
        add_action('wp_enqueue_scripts', array( $this, 'payment_scripts' ));
        add_action('woocommerce_api_wc_indodana_gateway', array(&$this, 'indodanaCallback'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Enable', 'indodana-enable-input-title'),
                'type'      => 'checkbox',
                'label'     => __('Check to enable the plugins', 'indodana-enable-input-label'),
                'default'   => 'no'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default'     => 'Indodana Payment',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default'     => 'Pay with your credit card via our super-cool payment gateway.',
            ),
            'environment' => array(
                'title'         => __('Environment', 'indodana-environment-input-title'),
                'type'          => 'select',
                'description'   => __('Choose development if you are testing this plugins', 'indodana-environment-input-label'),
                'default'       => 'development',
                'options'       => array(
                    'development'   => __('Development', 'indodana-environment-value-development'),
                    'production'    => __('Production', 'indodana-environment-value-production')
                )
            ),
            'api_key' => array(
                'title'         => __('Api Key', 'indodana-api-key-input-title'),
                'type'          => 'text',
                'description'   => __('Enter Api Key provided by Indodana', 'indodana-api-key-input-label'),
            ),
            'api_secret' => array(
                'title'         => __('Api Secret', 'indodana-api-key-input-title'),
                'type'          => 'text',
                'description'   => __('Enter Api Secret provided by Indodana', 'indodana-api-key-input-label'),
            )
        );
    }

    public function payment_fields()
    {
        echo wpautop(wp_kses_post($this->description));

        $this->indodanaApi = new IndodanaApi(
            $this->apiKey,
            $this->apiSecret,
            $this->isProduction
        );

        $items = array();

        $productObjects = $this->getProductObjectsFromCart(WC()->cart->get_cart());
        $items = array_merge($items, $productObjects);

        $totalAmount = $this->getTotalAmount($items);
        $paymentOptions = $this->indodanaApi->getPaymentOptions(
            $totalAmount,
            $items
        );

        $data = array();
        $data['paymentOptions'] = $paymentOptions;

        
        do_action('woocommerce_credit_card_form_start', $this->id);
        echo Renderer::render(ABSPATH . 'wp-content/plugins/indodana-payment/view/indodana-payment-form.php', $data);
        do_action('woocommerce_credit_card_form_end', $this->id);
    }

    private function getProductObjectsFromCart($cart)
    {
        $productObjects = array();
        foreach ($cart as $item) {
            $productObject = array(
                'id'        => '' . $item['product_id'],
                'url'       => '',
                'name'      => $item['data']->get_title(),
                'price'     => $item['data']->get_price(),
                'type'      => '',
                'quantity'  => $item['quantity']
            );
            $productObjects[] = $productObject;
        }

        return $productObjects;
    }

    private function getTotalAmount($items)
    {
        $price = 0;
        foreach ($items as $item) {
            $price += $item['price'];
        }

        return $price;
    }

    public function payment_scripts()
    {
    }

    public function validate_fields()
    {
    }

    public function process_payment($orderId)
    {
        IndodanaLogger::log(IndodanaLogger::INFO, print_r($_POST, true));
        $this->indodanaApi = new IndodanaApi(
            $this->apiKey,
            $this->apiSecret,
            $this->isProduction
        );

        $orderData = $this->generateOrderData($orderId);

        $paymentType = $_POST['paymentSelection'];
        $orderData['paymentType'] = $paymentType;

        try {
            $checkoutUrl = $this->indodanaApi->getCheckoutUrl($orderData);

            return array(
                'result' => 'success',
                'redirect' => $checkoutUrl
            );
        } catch (Exception $ex) {
            wc_add_notice('Connection error.');
            return;
        }
    }

    private function generateOrderData($orderId)
    {
        $order = wc_get_order($orderId);
        $transactionObject = $this->getTransactionObject($order, $orderId);
        $customerObject = $this->getCustomerObject($order);
        $billingObject = $this->getBillingObject($order);
        $shippingObject = $this->getShippingObject($order);

        $approvedNotificationUrl = add_query_arg(array(
            'wc-api'    => 'WC_Indodana_Gateway',
        ), home_url('/'));
        $cancellationRedirectUrl = add_query_arg(array(
            'wc-api'    => 'WC_Indodana_Gateway',
            'method'    => 'cancel',
            'order_id'  => $orderId
        ), home_url('/'));
        $backToStoreUrl = add_query_arg(array(
            'wc-api'    => 'WC_Indodana_Gateway',
            'method'    => 'complete',
            'order_id'  => $orderId
        ), home_url('/'));

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

    private function getProductObjectsFromOrder($order) {
        $productObjects = array();
        foreach ($order->get_items() as $itemId => $item) {
            $product = $item->get_product();
            $productObject = array(
                'id'        => '' . $itemId,
                'url'       => '',
                'name'      => $product->get_name(),
                'price'     => $product->get_price(),
                'type'      => '',
                'quantity'  => $item->get_quantity()
            );
            $productObjects[] = $productObject;
        }

        return $productObjects;
    }

    private function getTransactionObject($order, $orderId)
    {
        $items = array();
        $productObjects = $this->getProductObjectsFromOrder($order);
        $items = array_merge($items, $productObjects);

        $transactionObject = array(
            'merchantOrderId'   => $orderId,
            'items'             => $items,
            'amount'            => $this->getTotalAmount($items)
        );

        return $transactionObject;
    }

    private function getCustomerObject($order)
    {
        return array(
            'firstName' => $order->get_billing_first_name(),
            'lastName'  => $order->get_billing_last_name(),
            'email'     => $order->get_billing_email(),
            'phone'     => $order->get_billing_phone(),
        );
    }

    private function getShippingObject($order)
    {
        return array(
            'firstName'     => $order->get_shipping_first_name(),
            'lastName'      => $order->get_shipping_last_name(),
            'address'       => $order->get_shipping_address_1(),
            'city'          => $order->get_shipping_city(),
            'postalCode'    => $order->get_shipping_postcode(),
            'phone'         => $order->get_billing_phone(),
            'countryCode'   => $order->get_shipping_country(),
        );
    }

    private function getBillingObject($order)
    {
        return array(
            'firstName'     => $order->get_billing_first_name(),
            'lastName'      => $order->get_billing_last_name(),
            'address'       => $order->get_billing_address_1(),
            'city'          => $order->get_billing_city(),
            'postalCode'    => $order->get_billing_postcode(),
            'phone'         => $order->get_billing_phone(),
            'countryCode'   => $order->get_billing_country(),
        );
    }

    public function indodanaCallback()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleTransactionApproved();
            exit();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['method'])) {
                return;
            }

            $method = $_GET['method'];
            $orderId = $_GET['order_id'];
            switch ($method) {
                case 'cancel':
                    $this->handleRedirectDueToCancellation($orderId);
                    break;
                case 'complete':
                    $this->handleRedirectDueToCompletion($orderId);
                    break;
                default:
                    return;
            }
        }
    }

    private function handleTransactionApproved() {
        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $orderId = $postData['merchantOrderId'];
        $order = new WC_Order($orderId);

        $transactionStatus = $postData['transactionStatus'];
        switch($transactionStatus) {
            case 'INITIATED':
                $order->payment_complete();
                break;
            default:
                $order->update_status('on-hold');
        }

        header('Content-type: application/json');
        $response = array(
            'success'   => 'OK'
        );

        echo json_encode($response);
    }

    private function handleRedirectDueToCancellation($orderId) {
        $order = new WC_Order($orderId);
        wp_redirect($order->get_checkout_payment_url(false));
    }

    private function handleRedirectDueToCompletion($orderId) {
        $order = new WC_Order($orderId);
        wp_redirect($order->get_checkout_order_received_url());
    }

    public function convertCountryCode($country_code)
    {
        $countryCodemapper = array(
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
        if (isset($countryCodemapper[$countryCode]) && $countryCodemapper[$countryCode] != '') {
            $countryCode = $countryCodemapper[$countryCode];
        } else {
            $countryCode = '';
        }
        return $countryCode;
    }
}
