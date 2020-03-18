<?php

require_once INDODANA_PLUGIN_ROOT_DIR . 'autoload.php';

use Respect\Validation\Validator;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\MerchantResponse;

class WC_Indodana_Gateway extends WC_Payment_Gateway implements IndodanaInterface
{
  private static $country_code_options = [
    '' => 'Please select',
    'AFG' => 'AFG',
    'ALA' => 'ALA',
    'ALB' => 'ALB',
    'DZA' => 'DZA',
    'AND' => 'AND',
    'AGO' => 'AGO',
    'AIA' => 'AIA',
    'ATA' => 'ATA',
    'ATG' => 'ATG',
    'ARG' => 'ARG',
    'ARM' => 'ARM',
    'ABW' => 'ABW',
    'AUS' => 'AUS',
    'AUT' => 'AUT',
    'AZE' => 'AZE',
    'BHS' => 'BHS',
    'BHR' => 'BHR',
    'BGD' => 'BGD',
    'BRB' => 'BRB',
    'BLR' => 'BLR',
    'BEL' => 'BEL',
    'PLW' => 'PLW',
    'BLZ' => 'BLZ',
    'BEN' => 'BEN',
    'BMU' => 'BMU',
    'BTN' => 'BTN',
    'BOL' => 'BOL',
    'BES' => 'BES',
    'BIH' => 'BIH',
    'BWA' => 'BWA',
    'BVT' => 'BVT',
    'BRA' => 'BRA',
    'IOT' => 'IOT',
    'VGB' => 'VGB',
    'BRN' => 'BRN',
    'BGR' => 'BGR',
    'BFA' => 'BFA',
    'BDI' => 'BDI',
    'KHM' => 'KHM',
    'CMR' => 'CMR',
    'CAN' => 'CAN',
    'CPV' => 'CPV',
    'CYM' => 'CYM',
    'CAF' => 'CAF',
    'TCD' => 'TCD',
    'CHL' => 'CHL',
    'CHN' => 'CHN',
    'CXR' => 'CXR',
    'CCK' => 'CCK',
    'COL' => 'COL',
    'COM' => 'COM',
    'COG' => 'COG',
    'COD' => 'COD',
    'COK' => 'COK',
    'CRI' => 'CRI',
    'HRV' => 'HRV',
    'CUB' => 'CUB',
    'CUW' => 'CUW',
    'CYP' => 'CYP',
    'CZE' => 'CZE',
    'DNK' => 'DNK',
    'DJI' => 'DJI',
    'DMA' => 'DMA',
    'DOM' => 'DOM',
    'ECU' => 'ECU',
    'EGY' => 'EGY',
    'SLV' => 'SLV',
    'GNQ' => 'GNQ',
    'ERI' => 'ERI',
    'EST' => 'EST',
    'ETH' => 'ETH',
    'FLK' => 'FLK',
    'FRO' => 'FRO',
    'FJI' => 'FJI',
    'FIN' => 'FIN',
    'FRA' => 'FRA',
    'GUF' => 'GUF',
    'PYF' => 'PYF',
    'ATF' => 'ATF',
    'GAB' => 'GAB',
    'GMB' => 'GMB',
    'GEO' => 'GEO',
    'DEU' => 'DEU',
    'GHA' => 'GHA',
    'GIB' => 'GIB',
    'GRC' => 'GRC',
    'GRL' => 'GRL',
    'GRD' => 'GRD',
    'GLP' => 'GLP',
    'GTM' => 'GTM',
    'GGY' => 'GGY',
    'GIN' => 'GIN',
    'GNB' => 'GNB',
    'GUY' => 'GUY',
    'HTI' => 'HTI',
    'HMD' => 'HMD',
    'HND' => 'HND',
    'HKG' => 'HKG',
    'HUN' => 'HUN',
    'ISL' => 'ISL',
    'IND' => 'IND',
    'IDN' => 'IDN',
    'RIN' => 'RIN',
    'IRQ' => 'IRQ',
    'IRL' => 'IRL',
    'IMN' => 'IMN',
    'ISR' => 'ISR',
    'ITA' => 'ITA',
    'CIV' => 'CIV',
    'JAM' => 'JAM',
    'JPN' => 'JPN',
    'JEY' => 'JEY',
    'JOR' => 'JOR',
    'KAZ' => 'KAZ',
    'KEN' => 'KEN',
    'KIR' => 'KIR',
    'KWT' => 'KWT',
    'KGZ' => 'KGZ',
    'LAO' => 'LAO',
    'LVA' => 'LVA',
    'LBN' => 'LBN',
    'LSO' => 'LSO',
    'LBR' => 'LBR',
    'LBY' => 'LBY',
    'LIE' => 'LIE',
    'LTU' => 'LTU',
    'LUX' => 'LUX',
    'MAC' => 'MAC',
    'MKD' => 'MKD',
    'MDG' => 'MDG',
    'MWI' => 'MWI',
    'MYS' => 'MYS',
    'MDV' => 'MDV',
    'MLI' => 'MLI',
    'MLT' => 'MLT',
    'MHL' => 'MHL',
    'MTQ' => 'MTQ',
    'MRT' => 'MRT',
    'MUS' => 'MUS',
    'MYT' => 'MYT',
    'MEX' => 'MEX',
    'FSM' => 'FSM',
    'MDA' => 'MDA',
    'MCO' => 'MCO',
    'MNG' => 'MNG',
    'MNE' => 'MNE',
    'MSR' => 'MSR',
    'MAR' => 'MAR',
    'MOZ' => 'MOZ',
    'MMR' => 'MMR',
    'NAM' => 'NAM',
    'NRU' => 'NRU',
    'NPL' => 'NPL',
    'NLD' => 'NLD',
    'ANT' => 'ANT',
    'NCL' => 'NCL',
    'NZL' => 'NZL',
    'NIC' => 'NIC',
    'NER' => 'NER',
    'NGA' => 'NGA',
    'NIU' => 'NIU',
    'NFK' => 'NFK',
    'MNP' => 'MNP',
    'NOR' => 'NOR',
    'OMN' => 'OMN',
    'PAK' => 'PAK',
    'PSE' => 'PSE',
    'PAN' => 'PAN',
    'PNG' => 'PNG',
    'PRY' => 'PRY',
    'PER' => 'PER',
    'PHL' => 'PHL',
    'PCN' => 'PCN',
    'POL' => 'POL',
    'PRT' => 'PRT',
    'QAT' => 'QAT',
    'REU' => 'REU',
    'SHN' => 'SHN',
    'RUS' => 'RUS',
    'EWA' => 'EWA',
    'BLM' => 'BLM',
    'SHN' => 'SHN',
    'KNA' => 'KNA',
    'LCA' => 'LCA',
    'MAF' => 'MAF',
    'SXM' => 'SXM',
    'SPM' => 'SPM',
    'VCT' => 'VCT',
    'SMR' => 'SMR',
    'STP' => 'STP',
    'SAU' => 'SAU',
    'SEN' => 'SEN',
    'SRB' => 'SRB',
    'SYC' => 'SYC',
    'SLE' => 'SLE',
    'SGP' => 'SGP',
    'SVK' => 'SVK',
    'SVN' => 'SVN',
    'SLB' => 'SLB',
    'SOM' => 'SOM',
    'ZAF' => 'ZAF',
    'SGS' => 'SGS',
    'KOR' => 'KOR',
    'SSD' => 'SSD',
    'ESP' => 'ESP',
    'LKA' => 'LKA',
    'SDN' => 'SDN',
    'SUR' => 'SUR',
    'SJM' => 'SJM',
    'SWZ' => 'SWZ',
    'SWE' => 'SWE',
    'CHE' => 'CHE',
    'SYR' => 'SYR',
    'TWN' => 'TWN',
    'TJK' => 'TJK',
    'TZA' => 'TZA',
    'THA' => 'THA',
    'TLS' => 'TLS',
    'TGO' => 'TGO',
    'TKL' => 'TKL',
    'TON' => 'TON',
    'TTO' => 'TTO',
    'TUN' => 'TUN',
    'TUR' => 'TUR',
    'TKM' => 'TKM',
    'TCA' => 'TCA',
    'TUV' => 'TUV',
    'UGA' => 'UGA',
    'UKR' => 'UKR',
    'ARE' => 'ARE',
    'GBR' => 'GBR',
    'USA' => 'USA',
    'URY' => 'URY',
    'UZB' => 'UZB',
    'VUT' => 'VUT',
    'VAT' => 'VAT',
    'VEN' => 'VEN',
    'VNM' => 'VNM',
    'WLF' => 'WLF',
    'ESH' => 'ESH',
    'WSM' => 'WSM',
    'YEM' => 'YEM',
    'ZMB' => 'ZMB',
    'ZWE' => 'ZWE',
  ];

  private $indodana_service;

  public function __construct() {
    $this->id = 'indodana';
    $this->icon = '';
    $this->has_fields = true;

    $this->method_title = __('Indodana Payment', 'indodana-method-title');
    $this->method_description = __('Payment', 'indodana-method-description');

    $this->supports = array(
      'products'
    );

    $this->init_form_fields();
    $this->init_settings();

    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
    add_action('woocommerce_api_wc_indodana_gateway', array(&$this, 'indodana_callback'));
  }

  /**
   * Getter for indodana service
   *
   * We decide to "lazily" load indodana service because `get_option` might cause bug if we load this "eagerly"
   *
   * @return IndodanaService
   */
  private function get_indodana_service()
  {
    if (!isset($this->indodana_service)) {
      $this->indodana_service = new IndodanaService([
        'apiKey'      => $this->get_option('api_key'),
        'apiSecret'   => $this->get_option('api_secret'),
        'environment' => $this->get_option('environment'),
        'seller'      => $this->getSeller()
      ]);
    }

    return $this->indodana_service;
  }

  // Form on admin settings
  public function init_form_fields() {
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
        'description'   => __('Choose sandbox if you are testing this plugins', 'indodana-environment-input-label'),
        'default'       => 'SANDBOX',
        'options'       => array(
          'SANDBOX'   => __('Sandbox', 'indodana-environment-value-sandbox'),
          'PRODUCTION'    => __('Production', 'indodana-environment-value-production')
        )
      ),
      'store_name' => [
        'title'             => __('Store Name*', 'indodana-store-name-input-title'),
        'type'              => 'text',
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'Store Name is required',
      ],
      'store_url' => [
        'title'             => __('Store Url*', 'indodana-store-url-input-title'),
        'type'              => 'text',
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'Store Url is required',
      ],
      'store_email' => [
        'title'             => __('Store Email*', 'indodana-store-email-input-title'),
        'type'              => 'text',
        'validator'        => Validator::notOptional()->email(),
        'validationMessage' => 'Store Email is required and must be a valid email',
      ],
      'store_phone_number' => [
        'title'         => __('Store Phone Number*', 'indodana-store-phone-number-input-title'),
        'type'          => 'text',
        'validator'        => Validator::notOptional()->numeric(),
        'validationMessage' => 'Store Phone Number is required and must be numeric',
      ],
      'store_country_code' => [
        'title'         => __('Store Country Code*', 'indodana-store-country-code-input-title'),
        'type'          => 'select',
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'Store Country Code is required',
        'options' => self::$country_code_options,
      ],
      'store_city' => [
        'title'         => __('Store City*', 'indodana-store-city-input-title'),
        'type'          => 'text',
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'Store City is required',
      ],
      'store_address' => [
        'title'         => __('Store Address*', 'indodana-store-address-input-title'),
        'type'          => 'textarea',
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'Store Address is required',
      ],
      'store_postal_code' => [
        'title'         => __('Store Postal Code*', 'indodana-postal-code-input-title'),
        'type'          => 'text',
        'validator'        => Validator::notOptional()->length(null, 5),
        'validationMessage' => 'Store Postal Code is required and must have length 5',
      ],
      'api_key' => array(
        'title'         => __('Api Key*', 'indodana-api-key-input-title'),
        'type'          => 'text',
        'description'   => __('Enter Api Key provided by Indodana', 'indodana-api-key-input-label'),
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'API Key is required',
      ),
      'api_secret' => array(
        'title'         => __('Api Secret*', 'indodana-api-secret-input-title'),
        'type'          => 'text',
        'description'   => __('Enter Api Secret provided by Indodana', 'indodana-api-secret-input-label'),
        'validator'        => Validator::notOptional(),
        'validationMessage' => 'API Secret is required',
      ),
      'use_billing_address_for_shipping_address' => array(
        'title'             => __('Use Billing Address for Shipping Address*', 'indodana-use-billing-address-for-shipping-address-input-title'),
        'type'              => 'select',
        'default'           => 'no',
        'options'           => array(
          'no'    => __('No', 'indodana-use-billing-address-for-shipping-address-value-no'),
          'yes'   => __('Yes', 'indodana-use-billing-address-for-shipping-address-value-no')
        )
      )
    );
  }

  public function validate_field($key, $value) {
    $field = $this->form_fields[$key];

    if (!isset($field['validator'])) {
      return $value;
    }

    $validationResult = $field['validator']($value);

    if (!$validationResult) {
      WC_Admin_Settings::add_error($field['validationMessage']);
    }

    return $value;
  }

  public function validate_store_name_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_url_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_email_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_phone_number_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_country_code_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_city_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_address_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_store_postal_code_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_api_key_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function validate_api_secret_field($key, $value) {
    return $this->validate_field($key, $value);
  }

  public function getTotalAmount($cart) 
  {
    return (float) $cart->total;
  }

  public function getTotalDiscountAmount($cart)
  {
    return (float) $cart->get_discount_total($cart);
  }

  public function getTotalShippingAmount($cart)
  {
    return (float) $cart->get_shipping_total();
  }

  public function getTotalTaxAmount($cart)
  {
    return (float) $cart->get_total_tax();
  }

  public function getItems($cart)
  {
    $items = $cart->get_cart();

    $cartItems = [];

    foreach($items as $item) {
      $product = $item['data'];

      // Image might not exists
      $image_url = !empty($product->get_image_id()) ?
        wp_get_attachment_image_url($product->get_image_id(), 'full') :
        '';

      // Type might not exists
      $type = $product->get_type() ?? '';

      $cartItems[] = [
        'id' => (string) $product->get_id(),
        'name' => $product->get_title(),
        'price' => (float) $product->get_price(),
        'url' => get_permalink($product->get_id()),
        'imageUrl' => $image_url,
        'type' => $type,
        'quantity' => (int) $item['quantity'],
      ];
    }

    return $cartItems;
  }

  public function getCustomerDetails($order)
  {
    return [
      'firstName' => $order->get_billing_first_name(),
      'lastName'  => $order->get_billing_last_name(),
      'email'     => $order->get_billing_email(),
      'phone'     => $order->get_billing_phone(),
    ];
  }

  public function getBillingAddress($order)
  {
    return [
      'firstName'   => $order->get_billing_first_name(),
      'lastName'    => $order->get_billing_last_name(),
      'address'     => $order->get_billing_address_1(),
      'city'        => $order->get_billing_city(),
      'postalCode'  => $order->get_billing_postcode(),
      'phone'       => $order->get_billing_phone(),
      'countryCode' => $order->get_billing_country()
    ];
  }

  public function getShippingAddress($order)
  {
    if (strtolower($this->get_option('use_billing_address_for_shipping_address')) === 'yes') {
      return $this->getBillingAddress($order);
    }

    return [
      'firstName'   => $order->get_shipping_first_name(),
      'lastName'    => $order->get_shipping_last_name(),
      'address'     => $order->get_shipping_address_1(),
      'city'        => $order->get_shipping_city(),
      'postalCode'  => $order->get_shipping_postcode(),
      'phone'       => $order->get_billing_phone(),
      'countryCode' => $order->get_shipping_country(),
    ];
  }

  public function getSeller() {
    $store_name = $this->get_option('store_name');

    return [
      'name'    => $store_name,
      'email'   => $this->get_option('store_email'),
      'url'     => $this->get_option('store_url'),
      'address' => [
        'firstName'   => $store_name,
        'phone'       => $this->get_option('store_phone_number'),
        'address'     => $this->get_option('store_address'),
        'city'        => $this->get_option('store_city'),
        'postalCode'  => $this->get_option('store_postal_code'),
        'countryCode' => $this->get_option('store_country_code'),
      ]
    ];
  }

  public function payment_fields() {
    echo wpautop(wp_kses_post($this->description));

    $cart = WC()->cart;

    $payment_options = $this->get_indodana_service()->getInstallmentOptions([
      'totalAmount'    => $this->getTotalAmount($cart),
      'discountAmount' => $this->getTotalDiscountAmount($cart),
      'shippingAmount' => $this->getTotalShippingAmount($cart),
      'taxAmount'      => $this->getTotalTaxAmount($cart),
      'items'          => $this->getItems($cart)
    ]);

    $data = [];
    $data['paymentOptions'] = $payment_options;

    do_action('woocommerce_credit_card_form_start', $this->id);

    echo Renderer::render(ABSPATH . 'wp-content/plugins/indodana-payment/view/indodana-payment-form.php', $data);

    do_action('woocommerce_credit_card_form_end', $this->id);
  }

  public function process_payment($order_id) {
    IndodanaLogger::log(
      IndodanaLogger::INFO,
      sprintf('[process_payment] POST data %s', print_r($_POST, true))
    );

    $cart = WC()->cart;
    $order = wc_get_order($order_id);

    $approved_notification_url = add_query_arg(array(
      'wc-api'    => 'WC_Indodana_Gateway',
    ), home_url('/'));

    $cancellation_redirect_url = add_query_arg(array(
      'wc-api'    => 'WC_Indodana_Gateway',
      'method'    => 'cancel',
      'order_id'  => $order_id
    ), home_url('/'));

    $back_to_store_url = add_query_arg(array(
      'wc-api'    => 'WC_Indodana_Gateway',
      'method'    => 'complete',
      'order_id'  => $order_id
    ), home_url('/'));

    // DEV MODE
    // $approved_notification_url = add_query_arg(array(
      // 'wc-api'    => 'WC_Indodana_Gateway',
    // ), 'https://example.com');

    // $cancellation_redirect_url = add_query_arg(array(
      // 'wc-api'    => 'WC_Indodana_Gateway',
      // 'method'    => 'cancel',
      // 'order_id'  => $order_id
    // ), 'https://example.com');

    // $back_to_store_url = add_query_arg(array(
      // 'wc-api'    => 'WC_Indodana_Gateway',
      // 'method'    => 'complete',
      // 'order_id'  => $order_id
    // ), 'https://example.com');

    $checkout_url = $this->get_indodana_service()->checkout([
      'merchantOrderId'         => $order_id,
      'totalAmount'             => $this->getTotalAmount($cart),
      'discountAmount'          => $this->getTotalDiscountAmount($cart),
      'shippingAmount'          => $this->getTotalShippingAmount($cart),
      'taxAmount'               => $this->getTotalTaxAmount($cart),
      'items'                   => $this->getItems($cart),
      'customerDetails'         => $this->getCustomerDetails($order),
      'billingAddress'          => $this->getBillingAddress($order),
      'shippingAddress'         => $this->getShippingAddress($order),
      'paymentType'             => $_POST['payment_selection'],
      'approvedNotificationUrl' => $approved_notification_url,
      'cancellationRedirectUrl' => $cancellation_redirect_url,
      'backToStoreUrl'          => $back_to_store_url
    ]);

    WC()->cart->empty_cart();

    return [
      'result' => 'success',
      'redirect' => $checkout_url
    ];
  }

  public function indodana_callback() {
    // Approve notification url
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->handle_approved_transaction();

      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!isset($_GET['method']) && !isset($_GET['order_id'])) {
        return;
      }

      $method = $_GET['method'];
      $order_id = $_GET['order_id'];

      switch ($method) {
        case 'cancel': {
          $this->handle_redirect_cancellation($order_id); // Cancellation redirect url
          break;
        }
        case 'complete': {
          $this->handle_redirect_completion($order_id); // Back to store url
          break;
        }
      }
    }
  }

  private function handle_approved_transaction() {
    $namespace = '[Woocommerce-handle_approved_transaction]';

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

    $is_valid_authorization = $this->get_indodana_service()->isValidAuthToken($auth_token);

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
    $order = wc_get_order($order_id);

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

    $order->payment_complete();

    return MerchantResponse::printSuccessResponse($namespace);
  }

  private function handle_redirect_cancellation($order_id) {
    $order = new WC_Order($order_id);

    wp_redirect($order->get_checkout_payment_url(false));
  }

  private function handle_redirect_completion($order_id) {
    $order = new WC_Order($order_id);

    wp_redirect($order->get_checkout_order_received_url());
  }
}
