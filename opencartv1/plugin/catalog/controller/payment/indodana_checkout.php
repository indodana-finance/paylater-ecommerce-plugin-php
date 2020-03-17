<?php

require_once DIR_SYSTEM . 'library/indodana/autoload.php';

use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\MerchantResponse;

class ControllerPaymentIndodanaCheckout extends Controller implements IndodanaInterface
{
  private $indodanaApi;
  private $indodanaService;
  private $defaultCurrency;

  // We need this because the data sometimes have html entity such as &nbsp, etc.
  private static function decode($html_entity) {
    return html_entity_decode($html_entity, ENT_QUOTES, 'UTF-8');
  }

  private function getIndodanaService()
  {
    if (!isset($this->indodanaService)) {
      $apiKey = $this->config->get('indodana_checkout_api_key');
      $apiSecret = $this->config->get('indodana_checkout_api_secret');
      $environment = $this->config->get('indodana_checkout_environment');

      $this->indodanaService = new IndodanaService([
        'apiKey'        => $apiKey,
        'apiSecret'     => $apiSecret,
        'environment'   => $environment,
        'seller'        => $this->getSeller()
      ]);
    }

    return $this->indodanaService;
  }

  private function getDefaultCurrency()
  {
    if (!isset($this->defaultCurrency)) {
      $this->defaultCurrency = $this->config->get('config_currency');
    }

    return $this->defaultCurrency;
  }

  public function getTotalAmount($order)
  {
    $totalRows = $this->model_payment_indodana_checkout->getTotalRows($order['order_id']);

    return $this->getTotalValueOrderTotalRows($totalRows);
  }

  public function getTotalDiscountAmount($order)
  {
    $discountRows = $this->model_payment_indodana_checkout->getDiscountRows($order['order_id']);

    return $this->getTotalValueOrderTotalRows($discountRows);
  }

  public function getTotalShippingAmount($order)
  {
    $shippingRows = $this->model_payment_indodana_checkout->getShippingRows($order['order_id']);
    
    return $this->getTotalValueOrderTotalRows($shippingRows);
  }

  public function getTotalTaxAmount($order)
  {
    $taxRows = $this->model_payment_indodana_checkout->getTaxRows($order['order_id']);

    return $this->getTotalValueOrderTotalRows($taxRows);
  }

  private function getTotalValueOrderTotalRows($rows)
  {
    return array_reduce(
      $rows,
      function ($carry, $row) {
        // All value will be converted to IDR
        $value = $this->currency->convert((float) $row['value'], $this->getDefaultCurrency(), "IDR");

        $carry += abs($value);

        return $carry;
      },
      0
    );
  }

  public function getItems($order)
  {
    $items = [];

    $order_id = $order['order_id'];

    $order_products = $this->model_account_order->getOrderProducts($order_id);

    foreach ($order_products as $order_product) {
      $product_id = $order_product['product_id'];
      $product = $this->model_catalog_product->getProduct($product_id);

      // Get URL
      $url = $this->url->link(
        'product/product',
        'product_id=' . $product_id
      );

      // Get Image URL
      $imageUrl = $this->model_tool_image->resize(
        $product['image'],
        $this->config->get('config_image_popup_width'),
        $this->config->get('config_image_popup_height')
      );

      // Get type
      $type = '';

      $productCategories = $this->model_catalog_product->getCategories($product_id);

      if (!empty($productCategories)) {
        $category_id = $productCategories[0]['category_id'];

        $category = $this->model_catalog_category->getCategory($category_id);

        $type = $category['name'];
      }

      $items[] = [
        'id'        => self::decode($product_id),
        'name'      => self::decode($order_product['name']),
        'price'     => (float) self::decode($order_product['price']),
        'url'       => self::decode($url),
        'imageUrl'  => self::decode($imageUrl),
        'type'      => self::decode($type),
        'quantity'  => (int) self::decode($order_product['quantity']),
      ];
    }

    return $items;
  }

  public function getCustomerDetails($order) {
    return [
      'firstName' => self::decode($order['firstname']),
      'lastName' => self::decode($order['lastname']),
      'email' => self::decode($order['email']),
      'phone' => self::decode($order['telephone']),
    ];
  }

  public function getShippingAddress($order)
  {
    if (!$this->cart->hasShipping()) {
      return $this->getBillingAddress($order);
    }

    $firstName = self::decode($order['shipping_firstname']);
    $lastName = self::decode($order['shipping_lastname']);
    $address = self::decode($order['shipping_address_1']);
    $city = self::decode($order['shipping_city']);
    $postalCode = self::decode($order['shipping_postcode']);
    $phone = self::decode($order['telephone']);
    $countryCode = self::decode($order['payment_iso_code_3']);

    return [
      'firstName'     => $firstName,
      'lastName'      => $lastName,
      'address'       => $address,
      'city'          => $city,
      'postalCode'    => $postalCode,
      'phone'         => $phone,
      'countryCode'   => $countryCode
    ];
  }

  public function getBillingAddress($order)
  {
    $firstName = self::decode($order['payment_firstname']);
    $lastName = self::decode($order['payment_lastname']);
    $address = self::decode($order['payment_address_1']);
    $city = self::decode($order['payment_city']);
    $postalCode = self::decode($order['payment_postcode']);
    $phone = self::decode($order['telephone']);
    $countryCode = self::decode($order['payment_iso_code_3']);

    return [
      'firstName'     => $firstName,
      'lastName'      => $lastName,
      'address'       => $address,
      'city'          => $city,
      'postalCode'    => $postalCode,
      'phone'         => $phone,
      'countryCode'   => $countryCode
    ];
  }

  public function getSeller()
  {
    $name = $this->config->get('indodana_store_name');

    return [
      'name'    => $name,
      'email'   => $this->config->get('indodana_store_email'),
      'url'     => $this->config->get('indodana_store_url'),
      'address' => [
        'firstName'   => $name,
        'phone'       => $this->config->get('indodana_store_phone'),
        'address'     => $this->config->get('indodana_store_address'),
        'city'        => $this->config->get('indodana_store_city'),
        'postalCode'  => $this->config->get('indodana_store_postal_code'),
        'countryCode' => $this->config->get('indodana_store_country_code'),
      ]
    ];
  }

  public function notify()
  {
    $this->load->model('checkout/order');

    $namespace = '[OpencartV1-notify]';

    $request_headers = getallheaders();

    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Request headers: %s',
        $namespace,
        json_encode($request_headers)
      )
    );

    $auth_token = isset($request_headers['Authorization']) ? $request_headers['Authorization'] : '';

    $is_valid_authorization = $this->getIndodanaService()->isValidAuthToken($auth_token);

    if (!$is_valid_authorization) {
      return MerchantResponse::printInvalidRequestAuthResponse($namespace);
    }

    $request_body = IndodanaHelper::getRequestBody();

    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($request_body)
      )
    );

    if (!isset($request_body['transactionStatus']) || !isset($request_body['merchantOrderId'])) {
      return MerchantResponse::printInvalidRequestBodyResponse($namespace);
    }

    $transaction_status = $request_body['transactionStatus'];
    $order_id = $request_body['merchantOrderId'];
    $order = $this->model_checkout_order->getOrder($order_id);

    if (!$order) {
      return MerchantResponse::printNotFoundOrderResponse(
        $order_id,
        $namespace
      );
    }

    if (!in_array($transaction_status, IndodanaConstant::getSuccessTransactionStatus())) {
      return MerchantResponse::printInvalidTransactionStatusResponse(
        $transaction_status,
        $order_id,
        $namespace
      );
    }

    $this->handle_approved_transaction($order_id);

    return MerchantResponse::printSuccessResponse($namespace);
  }

  public function confirmOrder()
  {
    $this->load->model('checkout/order');

    $namespace = '[OpencartV1-confirmOrder]';

    $request_body = IndodanaHelper::getRequestBody();

    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($request_body)
      )
    );

    $order_id = $request_body['orderId'];

    $this->model_checkout_order->confirm(
      $order_id,
      $this->config->get('indodana_checkout_default_order_pending_status_id')
    );

    return MerchantResponse::printSuccessResponse($namespace);
  }

  public function cancel()
  {
    $this->load->model('checkout/order');

    $namespace = '[OpencartV1-cancel]';

    $request_body = IndodanaHelper::getRequestBody();

    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($request_body)
      )
    );

    $order_id = $request_body['merchantOrderId'];

    $this->model_checkout_order->update(
      $order_id,
      $this->config->get('indodana_checkout_default_order_failed_status_id')
    );

    $this->redirect($this->url->link(''));
  }

  private function handle_approved_transaction($order_id)
  {
    $this->model_checkout_order->update(
      $order_id,
      $this->config->get('indodana_checkout_default_order_success_status_id'),
      'Indodana payment successful'
    );
  }

  public function index()
  {
    $this->loadModel();
    $this->loadLanguageData();

    $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    $indodanaService = $this->getIndodanaService();

    $paymentOptions = $indodanaService->getInstallmentOptions([
      'totalAmount'    => $this->getTotalAmount($order),
      'discountAmount' => $this->getTotalDiscountAmount($order),
      'shippingAmount' => $this->getTotalShippingAmount($order),
      'taxAmount'      => $this->getTotalTaxAmount($order),
      'items'          => $this->getItems($order)
    ]);

    $this->formatPaymentsToDefaultCurrency($paymentOptions);

    $approvedNotificationUrl = $this->url->link('payment/indodana_checkout/notify');
    $cancellationRedirectUrl = $this->url->link('payment/indodana_checkout/cancel');
    $backToStoreUrl = $this->url->link('checkout/success');

    // DEV MODE
    // $approvedNotificationUrl = 'https://example.com/index.php?route=payment/indodana_checkout/notify';
    // $cancellationRedirectUrl = 'https://example.com/index.php?route=payment/indodana_checkout/cancel';
    // $backToStoreUrl = 'https://example.com/index.php?route=checkout/success';

    $this->initializePaymentOptions($paymentOptions);

    $orderData = $indodanaService->getCheckoutPayload([
      'merchantOrderId'         => $order['order_id'],
      'totalAmount'             => $this->getTotalAmount($order),
      'discountAmount'          => $this->getTotalDiscountAmount($order),
      'shippingAmount'          => $this->getTotalShippingAmount($order),
      'taxAmount'               => $this->getTotalTaxAmount($order),
      'items'                   => $this->getItems($order),
      'customerDetails'         => $this->getCustomerDetails($order),
      'billingAddress'          => $this->getBillingAddress($order),
      'shippingAddress'         => $this->getShippingAddress($order),
      'approvedNotificationUrl' => $approvedNotificationUrl,
      'cancellationRedirectUrl' => $cancellationRedirectUrl,
      'backToStoreUrl'          => $backToStoreUrl
    ]);

    $authorizationToken = $this->getIndodanaService()->getAuthToken();

    $this->data['orderData'] = json_encode($orderData);
    $this->data['paymentOptions'] = $paymentOptions;
    $this->data['authorization'] = $this->getIndodanaService()->getAuthToken();
    $this->data['indodanaBaseUrl'] = $indodanaService->getBaseUrl();
    $this->data['merchantConfirmPaymentUrl'] = $this->url->link('payment/indodana_checkout/confirmOrder');

    $this->template = $this->config->get('config_template') . '/template/payment/indodana_checkout_payment.tpl';
    $this->response->setOutput($this->render());
  }

  public function loadModel() {
    $this->language->load('payment/indodana_checkout');
    $this->load->model('account/order');
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('checkout/order');
    $this->load->model('payment/indodana_checkout');
    $this->load->model('setting/setting');
    $this->load->model('tool/image');
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
    $this->data['authorization'] = $bearer;
  }

  public function initializePaymentOptions($paymentOptions) {
    $this->data['paymentOptions'] = $paymentOptions;
  }

  public function initializeContextUrl() {
    $this->data['indodanaBaseUrl'] = $this->indodanaApi->getBaseUrl();
    $this->data['merchantConfirmPaymentUrl'] = $this->url->link('payment/indodana_checkout/confirmOrder');
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
}

