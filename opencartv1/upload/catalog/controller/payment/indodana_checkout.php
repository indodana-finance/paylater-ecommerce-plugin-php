<?php
require_once DIR_SYSTEM . 'library/indodana/autoload.php';

class ControllerPaymentIndodanaCheckout extends Controller 
{
    private $indodanaApi;

    public function notify() 
    {
        $this->load->model('checkout/order');

        $postData = IndodanaHelper::getJsonPost();
        $transactionStatus = $postData['status'];
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));
        // switch($transactionStatus) {
        //     case 'SUCCESS':
        //         $this->handlePaymentSuccess($postData['merchantOrderId']);
        //     case 'WAITING_FOR_APPROVAL':

        // }
    }

    private function handlePaymentSuccess($orderId)
    {
        $this->model_checkout_order->update(
            $orderId,
            'SUCCESS',
            'Payment Successful'
        );
    }

    public function index() 
    {
        $this->loadModel();
        $this->loadLanguageData();

        $apiKey = $this->config->get('indodana_checkout_api_key');
        $apiSecret = $this->config->get('indodana_checkout_api_secret');
        $this->indodanaApi = new IndodanaApi($apiKey, $apiSecret);

        $orderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $items = $this->getAllItemObjects($this->cart, $orderInfo['order_id']);
        $amount = self::calculateTotalPrice($items);
        try {
            $paymentOptions = $this->indodanaApi->getPaymentOptions($amount, $items);
        } catch (Exception $ex) {
            $paymentOptions = array();
        } finally {
            $this->formatPaymentsToDefaultCurrency($paymentOptions);
            $this->loadPaymentOptions($paymentOptions);
        }

        $orderData = $this->generateOrderData($orderInfo, $items, $amount);
        $json = json_encode($orderData);
        $this->loadOrderData($json);
        $this->loadAuthorization(IndodanaApi::generateBearer($apiKey, $apiSecret));

        $this->template = $this->config->get('config_template') . '/template/payment/indodana_checkout_payment.tpl';
        $this->response->setOutput($this->render());    
    }

    public function loadModel() {
        $this->language->load('payment/indodana_checkout');
        $this->load->model('checkout/order');
        $this->load->model('payment/indodana_checkout');
        $this->load->model('setting/setting');
    }

    public function loadLanguageData() {
        $this->data['textButtonConfirm'] = $this->language->get('text_button_confirm');
        $this->data['textPaymentOptions'] = $this->language->get('text_payment_options');
        $this->data['textPaymentOptionsName'] = $this->language->get('text_payment_options_name');
        $this->data['textPaymentOptionsMonthlyInstallment'] = $this->language->get('text_payment_options_monthly_installment');
        $this->data['textPaymentOptionsTotalAmount'] = $this->language->get('text_payment_options_total_amount');
    }

    private function loadOrderData($orderData) {
        $this->data['orderData'] = $orderData;
    }

    private function loadAuthorization($bearer) {
        $this->data['authorization'] = 'Bearer ' . $bearer;
    }

    public function loadPaymentOptions($paymentOptions) {
        $this->data['paymentOptions'] = $paymentOptions;
    }
    
    private function getAllItemObjects($cart, $orderId) {
        $items = array();
        $this->addProducts($items, $cart->getProducts());
        $this->addShipping($items, $orderId);
        $this->addTaxes($items, $orderId);
        
        return $items;
    }
    
    public function addShipping(&$items, $orderId) {
        $shipping = $this->model_payment_indodana_checkout->getShippingDetail($orderId);
        $shippingItemObject = array(
            'id' => 'shippingfee',
            'url' => '',
            'name' => $shipping['title'],
            'price' => ceil($this->currency->convert((float) $shipping['value'], $this->currency->getCode(), "IDR")),
            'type' => '',
            'quantity' => 1
        );
        array_push($items, $shippingItemObject);
    }
    
    public function addTaxes(&$items, $orderId) {
        $taxes = $this->model_payment_indodana_checkout->getTaxes($orderId);
        $totalTax = 0;
        foreach($taxes->rows as $tax) {
            $totalTax += $this->currency->convert((float) $tax['value'], $this->currency->getCode(), 'IDR');
        }
        $taxItemObject = array(
            'id' => 'taxfee',
            'url' => '',
            'name' => 'TAAAAAAAAAAAXXXXXXXXXXX',
            'price' => ceil($totalTax),
            'type' => '',
            'quantity' => 1
        );
        array_push($items, $taxItemObject);
    }
    
    public function addProducts(&$items, $products) {
        foreach($products as $product) {
            $productItemObject = array(
                'id' => $product['product_id'],
                'url' => '',
                'name' => $product['name'],
                'price' => ceil($this->currency->convert($product['price'], $this->currency->getCode(), 'IDR')),
                'type' => '',
                'quantity' => $product['quantity']
            );
            array_push($items, $productItemObject);
        }
        return $items;
    }

    private static function calculateTotalPrice($items) {
        $total = 0;
        foreach($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public function formatPaymentsToDefaultCurrency(&$payments) {
        foreach ($payments as &$payment) {
            $monthlyInstallment = $payment['monthlyInstallment'];
            $installmentAmount = $payment['installmentAmount'];
            
            $monthlyInstallment = $this->currency->convert($monthlyInstallment, 'IDR', $this->currency->getCode());
            $installmentAmount = $this->currency->convert($installmentAmount, 'IDR', $this->currency->getCode());
            
            $payment['monthlyInstallment'] = $this->currency->format($monthlyInstallment, $this->currency->getCode());
            $payment['installmentAmount'] = $this->currency->format($installmentAmount, $this->currency->getCode());
        }
    }

    public function generateOrderData($orderInfo, $items, $amount) {
        $transactionDetails = self::getTransactionDetails($orderInfo, $items, $amount);
        $customerDetails = self::getCustomerDetails($orderInfo);
        $billingAddress = self::getBillingAddress($orderInfo);
        $shippingAddress = array();
        if ($this->cart->hasShipping()) {
            $shippingAddress = self::getShippingAddress($orderInfo);
        } else {
            $shippingAddress = $billingAddress;
        }
        $approvedNotificationUrl = $this->getNotificationUrl();
        $cancellationRedirectUrl = $this->getCancellationRedirectUrl();
        $backToStoreUrl = $this->getBackToStoreUrl();

        $orderData = array(
            'transactionDetails'        => $transactionDetails,
            'customerDetails'           => $customerDetails,
            'billingAddress'            => $billingAddress,
            'shippingAddress'           => $shippingAddress,
            'customerDetails'           => $customerDetails,
            'approvedNotificationUrl'   => 'https://webhook.site/bc11eee8-d445-4ac0-b405-2ea94fb1e856',
            'cancellationRedirectUrl'   => $cancellationRedirectUrl,
            'backToStoreUrl'            => $backToStoreUrl
        );

        return $orderData;
    }

    private static function getTransactionDetails($orderInfo, $items, $amount) {
        $transactionDetails = array(
            'merchantOrderId'   => $orderInfo['order_id'],
            'amount'            => $amount,
            'items'             => $items
        );

        return $transactionDetails;
    }

    private static function getCustomerDetails($orderInfo)
    {
        $firstName = self::decode($orderInfo['firstname']);
        $lastName = self::decode($orderInfo['lastname']);
        $email = self::decode($orderInfo['email']);
        $phone = self::decode($orderInfo['telephone']);
        $customerDetails = array(
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'email'     => $email,
            'phone'     => $phone
        );
        
        return $customerDetails;
    }
    
    private static function getBillingAddress($orderInfo)
    {
        $firstName = self::decode($orderInfo['payment_firstname']);
        $lastName = self::decode($orderInfo['payment_lastname']);
        $address = self::decode($orderInfo['payment_address_1']);
        $city = self::decode($orderInfo['payment_city']);
        $postalCode = self::decode($orderInfo['payment_postcode']);
        $phone = self::decode($orderInfo['telephone']);
        $countryCode = self::decode($orderInfo['payment_iso_code_3']);
        $billingAddress = array(
            'firstName'     => $firstName,
            'lastName'      => $lastName,
            'address'       => $address,
            'city'          => $city,
            'postalCode'    => $postalCode,
            'phone'         => $phone,
            'countryCode'   => $countryCode
        );
        
        return $billingAddress;
    }
    
    private static function getShippingAddress($orderInfo) 
    {
        $firstName = self::decode($orderInfo['shipping_firstname']);
        $lastName = self::decode($orderInfo['shipping_lastname']);
        $address = self::decode($orderInfo['shipping_address_1']);
        $city = self::decode($orderInfo['shipping_city']);
        $postalCode = self::decode($orderInfo['shipping_postcode']);
        $phone = self::decode($orderInfo['telephone']);
        $countryCode = self::decode($orderInfo['payment_iso_code_3']);
        $shippingAddress = array(
            'firstName'     => $firstName,
            'lastName'      => $lastName,
            'address'       => $address,
            'city'          => $city,
            'postalCode'    => $postalCode,
            'phone'         => $phone,
            'countryCode'   => $countryCode
        );
        
        return $shippingAddress;
    }
    
    private static function decode($html_entity) {
        return html_entity_decode($html_entity, ENT_QUOTES, 'UTF-8');
    }
    
    private function getBackToStoreUrl()
    {
        return $this->url->link('payment/indodana/landing');   
    }
    
    private function getCancellationRedirectUrl()
    {
        return $this->url->link('payment/indodana/cancel');
    }
    
    private function getNotificationUrl()
    {
        return $this->url->link('payment/indodana/notify');
    }
}