<?php
require_once DIR_SYSTEM . 'library/indodana/autoload.php';

class ControllerPaymentIndodanaCheckout extends Controller
{
    private $indodanaApi;

    public function notify()
    {
        $this->load->model('checkout/order');

        $apiKey = $this->config->get('indodana_checkout_api_key');
        $apiSecret = $this->config->get('indodana_checkout_api_secret');
        $this->indodanaApi = new IndodanaApi($apiKey, $apiSecret);

        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $transactionStatus = $postData['transactionStatus'];
        $orderId = $postData['merchantOrderId'];

        $transactionSuccessful = $this->indodanaApi->checkIfTransactionSuccessful($orderId);
        if ($transactionSuccessful) {
            $this->handlePaymentSuccess($orderId);
            $this->handlePaymentExpired($orderId);
        } else {
        }

        header('Content-type: application/json');

        $response = array(
            'status'   => 'OK',
            'message' => 'OK'
        );

        echo json_encode($response);
    }

    public function confirmOrder()
    {
        $this->load->model('checkout/order');

        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $orderId = $postData['orderId'];

        $this->model_checkout_order->confirm(
            $orderId,
            $this->config->get('indodana_checkout_default_order_pending_status_id')
        );

        header('Content-type: application/json');

        $response = array(
            'success' => 'OK'
        );

        echo json_encode($response);
    }

    public function cancel()
    {
        $this->load->model('checkout/order');

        $postData = IndodanaHelper::getJsonPost();
        IndodanaLogger::log(IndodanaLogger::INFO, json_encode($postData));

        $orderId = $postData['merchantOrderId'];

        $this->model_checkout_order->update(
            $orderId,
            $this->config->get('indodana_checkout_default_order_failed_status_id')
        );

        $this->redirect($this->url->link(''));
    }

    private function handlePaymentSuccess($orderId)
    {
        $this->model_checkout_order->update(
            $orderId,
            $this->config->get('indodana_checkout_default_order_success_status_id'),
            'Indodana payment successful'
        );
    }

    private function handlePaymentExpired($orderId)
    {
        $this->model_checkout_order->update(
            $orderId,
            $this->config->get('indodana_checkout_default_order_failed_status_id'),
            'Indodana payment expired'
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
            $this->initializePaymentOptions($paymentOptions);
        }

        $orderData = $this->generateOrderData($orderInfo, $items, $amount);
        $json = json_encode($orderData);
        $this->initializeOrderData($json);
        $this->initializeAuthorization(IndodanaApi::generateBearer($apiKey, $apiSecret));
        $this->initializeContextUrl();

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

    private function initializeOrderData($orderData) {
        $this->data['orderData'] = $orderData;
    }

    private function initializeAuthorization($bearer) {
        $this->data['authorization'] = 'Bearer ' . $bearer;
    }

    public function initializePaymentOptions($paymentOptions) {
        $this->data['paymentOptions'] = $paymentOptions;
    }

    public function initializeContextUrl() {
        $this->data['indodanaBaseUrl'] = $this->indodanaApi->getBaseUrl();
        $this->data['merchantConfirmPaymentUrl'] = $this->url->link('payment/indodana_checkout/confirmOrder');
    }

    private function getAllItemObjects($cart, $orderId) {
        $itemObjects = array();
        $defaultCurrency = $this->config->get('config_currency');

        $productObjects = $this->convertProductsToIDR($cart->getProducts(), $defaultCurrency);
        $itemObjects = array_merge($itemObjects, $productObjects);

        if ($this->cart->hasShipping()) {
            $shippingObject = $this->getShippingInIDR($orderId, $defaultCurrency);
            array_push($itemObjects, $shippingObject);
        }

        $taxObject = $this->getTaxInIDR($orderId, $defaultCurrency);
        if ($taxObject != null) {
            array_push($itemObjects, $taxObject);
        }

        return $itemObjects;
    }

    public function getShippingInIDR($orderId, $currency) {
        $shipping = $this->model_payment_indodana_checkout->getShippingDetail($orderId);

        $shippingObject = array(
            'id' => 'shippingfee',
            'url' => '',
            'name' => $shipping['title'],
            'price' => ceil($this->currency->convert((float) $shipping['value'], $currency, "IDR")),
            'type' => '',
            'quantity' => 1
        );

        return $shippingObject;
    }

    public function getTaxInIDR($orderId, $currency) {
        $taxes = $this->model_payment_indodana_checkout->getTaxes($orderId);

        $totalTax = 0;
        foreach($taxes->rows as $tax) {
            $totalTax += $this->currency->convert((float) $tax['value'], $currency, 'IDR');
        }

        if ($totalTax == 0) {
            return null;
        }

        $taxObject = array(
            'id' => 'taxfee',
            'url' => '',
            'name' => 'TAAAAAAAAAAAXXXXXXXXXXX',
            'price' => ceil($totalTax),
            'type' => '',
            'quantity' => 1
        );

        return $taxObject;
    }

    public function convertProductsToIDR($products, $currency) {
        $productObjects = array();
        foreach($products as $product) {
            $productObject = array(
                'id' => $product['product_id'],
                'url' => '',
                'name' => $product['name'],
                'price' => ceil($this->currency->convert($product['price'], $currency, 'IDR')),
                'type' => '',
                'quantity' => $product['quantity']
            );
            array_push($productObjects, $productObject);
        }
        return $productObjects;
    }

    private static function calculateTotalPrice($items) {
        $total = 0;
        foreach($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function formatPaymentsToDefaultCurrency(&$payments) {
        $currency = $this->config->get('config_currency');
        foreach ($payments as &$payment) {
            $monthlyInstallment = $payment['monthlyInstallment'];
            $installmentAmount = $payment['installmentAmount'];

            $monthlyInstallment = $this->currency->convert($monthlyInstallment, 'IDR', $currency);
            $installmentAmount = $this->currency->convert($installmentAmount, 'IDR', $currency);

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
            'approvedNotificationUrl'   => $approvedNotificationUrl,
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
        return $this->url->link('checkout/success');
    }

    private function getCancellationRedirectUrl()
    {
        return $this->url->link('payment/indodana_checkout/cancel');
    }

    private function getNotificationUrl()
    {
        return $this->url->link('payment/indodana_checkout/notify');
    }
}

