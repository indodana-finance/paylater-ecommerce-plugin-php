<?php

require_once INDODANA_PLUGIN_ROOT_DIR . 'autoload.php';

use Respect\Validation\Validator;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\MerchantResponse;

class WC_Indodana_Gateway extends WC_Payment_Gateway implements IndodanaInterface
{
  private $indodana_common;

  public function __construct() {
    $this->id = 'indodana';
    $this->title = __($this->get_option('title'), 'indodana-title');
    $this->description = __($this->get_option('description'), 'indodana-description');
    $this->icon = __(IndodanaConstant::LOGO_URL, 'indodana-icon');
    $this->has_fields = true;

    $this->method_title = __('Indodana PayLater', 'indodana-method-title');
    $this->method_description = __('Indodana PayLater redirects customers to Indodana during checkout.', 'indodana-method-description');

    $this->supports = array(
      'products'
    );

    $this->init_form_fields();
    $this->init_settings();

    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
    add_action('woocommerce_api_wc_indodana_gateway', array(&$this, 'indodana_callback'));
  }

  /**
   * Getter for indodana common
   *
   * We decide to "lazily" load indodana common because `get_option` might cause bug if we load this "eagerly"
   *
   * @return IndodanaCommon
   */
  private function get_indodana_common()
  {
    if (!isset($this->indodana_common)) {
      $this->indodana_common = new IndodanaCommon([
        'apiKey'      => $this->get_option('api_key'),
        'apiSecret'   => $this->get_option('api_secret'),
        'environment' => $this->get_option('environment'),
        'seller'      => $this->getSeller()
      ]);
    }

    return $this->indodana_common;
  }

  /**
   * Form on admin settings
   */
  public function init_form_fields() {
    $order_statuses = wc_get_order_statuses();

    $this->form_fields = array(
      'enabled' => [
        'title'           => 'Enable',
        'type'            => 'checkbox',
        'label'           => 'Check to enable the plugins',
        'default'         => 'no',
        'config_name'     => 'status',
      ],
      'title' => [
        'title'           => 'Title',
        'type'            => 'text',
        'description'     => 'This controls the title which the user sees during checkout.',
        'desc_tip'        => true,
        'default'         => 'Indodana PayLater',
      ],
      'description' => [
        'title'           => 'Description',
        'type'            => 'textarea',
        'description'     => 'This controls the description which the user sees during checkout.',
        'desc_tip'        => true,
        'default'         => 'Pay with installment via our PayLater product.',
      ],
      'environment' => [
        'title'           => 'Environment',
        'type'            => 'select',
        'description'     => 'Choose "Sandbox" if you are testing this plugins',
        'default'         => IndodanaConstant::SANDBOX,
        'options'         => IndodanaConstant::getEnvironmentMapping(),
        'config_name'     => 'environment',
      ],
      'store_name' => [
        'title'           => 'Store Name',
        'type'            => 'text',
        'config_name'     => 'storeName',
      ],
      'store_url' => [
        'title'           => 'Store URL',
        'type'            => 'text',
        'config_name'     => 'storeUrl',
      ],
      'store_email' => [
        'title'           => 'Store Email',
        'type'            => 'text',
        'config_name'     => 'storeEmail',
      ],
      'store_phone' => [
        'title'           => 'Store Phone',
        'type'            => 'text',
        'config_name'     => 'storePhone',
      ],
      'store_country_code' => [
        'title'           => 'Store Country Code',
        'type'            => 'select',
        'options'         => IndodanaConstant::getCountryCodeMapping(),
        'config_name'     => 'storeCountryCode',
      ],
      'store_city' => [
        'title'           => 'Store City',
        'type'            => 'text',
        'config_name'     => 'storeCity',
      ],
      'store_address' => [
        'title'           => 'Store Address',
        'type'            => 'textarea',
        'config_name'     => 'storeAddress',
      ],
      'store_postal_code' => [
        'title'           => 'Store Postal Code',
        'type'            => 'text',
        'config_name'     => 'storePostalCode',
      ],
      'api_key' => [
        'title'           => 'API Key',
        'type'            => 'text',
        'description'     => 'Enter API Key provided by Indodana',
        'config_name'     => 'apiKey',
      ],
      'api_secret' => [
        'title'           => 'API Secret',
        'type'            => 'text',
        'description'     => 'Enter API Secret provided by Indodana',
        'config_name'     => 'apiSecret',
      ],
      'default_order_pending_status' => [
        'title'           => 'Default Order Pending Status',
        'type'            => 'select',
        'options'         => $order_statuses,
        'config_name'     => 'defaultOrderPendingStatus',
      ],
      'default_order_success_status' => [
        'title'           => 'Default Order Success Status',
        'type'            => 'select',
        'options'         => $order_statuses,
        'config_name'     => 'defaultOrderSuccessStatus',
      ],
      'default_order_failed_status' => [
        'title'           => 'Default Order Failed Status',
        'type'            => 'select',
        'options'         => $order_statuses,
        'config_name'     => 'defaultOrderFailedStatus',
      ],
    );
  }

  /**
	 * Processes and saves options.
   *
   * Override from `wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-settings-api.php` with minor tweaks.
   * This function is used to validate form on Indodana configuration
   *
	 * @return bool was anything saved?
   */
  public function process_admin_options()
  {
    $this->init_settings();

		$post_data = $this->get_post_data();

    // Validate configuration form data
    // -----
    $configuration = [];

    foreach ($this->get_form_fields() as $key => $field) {
      // We won't validate these fields
      if (!in_array($key, [ 'enabled', 'title', 'description' ])) {
        $configuration[$field['config_name']] = $this->get_field_value( $key, $field, $post_data );
      }

			if ('title' !== $this->get_field_type( $field )) {
        $this->settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
      }
    }

    $validation_result = IndodanaCommon::validateConfiguration($configuration);
    $validation_errors = $validation_result['errors'];

    if (!empty($validation_errors)) {
      foreach ($validation_errors as $key => $field) {
        WC_Admin_Settings::add_error($field);
      }

      return false;
    }

    // Save configuration
    // -----
    return update_option(
      $this->get_option_key(),
      apply_filters(
        'woocommerce_settings_api_sanitized_fields_' . $this->id,
        $this->settings
      ),
      'yes'
    );
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

  public function getProducts($cart)
  {
    $cart_items = $cart->get_cart();

    $products = [];

    foreach($cart_items as $cart_item) {
      $product = $cart_item['data'];

      // Image might not exists
      $image_url = !empty($product->get_image_id()) ?
        wp_get_attachment_image_url($product->get_image_id(), 'full') :
        '';

      // Type might not exists
      $type = $product->get_type() ?? '';

      $products[] = [
        'id' => (string) $product->get_id(),
        'name' => $product->get_title(),
        'price' => (float) $product->get_price(),
        'url' => get_permalink($product->get_id()),
        'imageUrl' => $image_url,
        'type' => $type,
        'quantity' => (int) $cart_item['quantity'],
      ];
    }

    return $products;
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
    $seller_name = $this->get_option('store_name');

    return [
      'name'    => $seller_name,
      'email'   => $this->get_option('store_email'),
      'url'     => $this->get_option('store_url'),
      'address' => [
        'firstName'   => $seller_name,
        'phone'       => $this->get_option('store_phone'),
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

    $payment_options = $this->get_indodana_common()->getInstallmentOptions([
      'totalAmount'    => $this->getTotalAmount($cart),
      'discountAmount' => $this->getTotalDiscountAmount($cart),
      'shippingAmount' => $this->getTotalShippingAmount($cart),
      'taxAmount'      => $this->getTotalTaxAmount($cart),
      'products'       => $this->getProducts($cart)
    ]);

    $data = [];
    $data['paymentOptions'] = $payment_options;

    do_action('woocommerce_credit_card_form_start', $this->id);

    echo Renderer::render(ABSPATH . 'wp-content/plugins/indodana-payment/view/indodana-payment-form.php', $data);

    do_action('woocommerce_credit_card_form_end', $this->id);
  }

  public function process_payment($order_id) {
    $namespace = '[Woocommerce-process_payment]';

    IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($_POST)
      )
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

    $checkout_url = $this->get_indodana_common()->checkout([
      'merchantOrderId'         => $order_id,
      'totalAmount'             => $this->getTotalAmount($cart),
      'discountAmount'          => $this->getTotalDiscountAmount($cart),
      'shippingAmount'          => $this->getTotalShippingAmount($cart),
      'taxAmount'               => $this->getTotalTaxAmount($cart),
      'products'                => $this->getProducts($cart),
      'customerDetails'         => $this->getCustomerDetails($order),
      'billingAddress'          => $this->getBillingAddress($order),
      'shippingAddress'         => $this->getShippingAddress($order),
      'paymentType'             => $_POST['payment_selection'],
      'approvedNotificationUrl' => $approved_notification_url,
      'cancellationRedirectUrl' => $cancellation_redirect_url,
      'backToStoreUrl'          => $back_to_store_url
    ]);

    $order->update_status($this->get_option('default_order_pending_status'));

    WC()->cart->empty_cart();

    return [
      'result' => 'success',
      'redirect' => $checkout_url
    ];
  }

  public function indodana_callback() {
    // Approve notification url
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->handle_notify_action();

      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!isset($_GET['method']) && !isset($_GET['order_id'])) {
        return;
      }

      $method = $_GET['method'];
      $order_id = $_GET['order_id'];

      switch ($method) {
        // Cancellation redirect url
        case 'cancel': {
          $this->handle_cancel_action($order_id);
          break;
        }
        // Back to store url
        case 'complete': {
          $this->handle_complete_action($order_id);
          break;
        }
      }
    }
  }

  private function handle_cancel_action($order_id) {
    $order = wc_get_order($order_id);

    $order->update_status($this->get_option('default_order_failed_status'));

    wp_redirect($order->get_cancel_order_url());
  }

  private function handle_complete_action($order_id) {
    $order = wc_get_order($order_id);

    wp_redirect($order->get_checkout_order_received_url());
  }

  private function handle_notify_action() {
    // Log request headers
    // -----
    $namespace = '[Woocommerce-handle_notify_action]';

    $request_headers = IndodanaHelper::getRequestHeaders();

    IndodanaLogger::info(
      sprintf(
        '%s Request headers: %s',
        $namespace,
        json_encode($request_headers)
      )
    );

    // Check whether request authorization is valid
    // -----
    $auth_token = isset($request_headers['Authorization']) ? $request_headers['Authorization'] : '';

    $is_valid_authorization = $this->get_indodana_common()->isValidAuthToken($auth_token);

    if (!$is_valid_authorization) {
      MerchantResponse::printInvalidRequestAuthResponse($namespace);

      return;
    }

    // Log request body
    // -----
    $request_body = IndodanaHelper::getRequestBody();

    IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($request_body)
      )
    );

    // Check whether request body is valid
    // -----
    if (!isset($request_body['transactionStatus']) || !isset($request_body['merchantOrderId'])) {
      MerchantResponse::printInvalidRequestBodyResponse($namespace);

      return;
    }

    $transaction_status = $request_body['transactionStatus'];
    $order_id = $request_body['merchantOrderId'];

    $order = wc_get_order($order_id);

    if (!$order) {
      MerchantResponse::printNotFoundOrderResponse(
        $order_id,
        $namespace
      );

      return;
    }

    if (!in_array($transaction_status, IndodanaConstant::getSuccessTransactionStatuses())) {
      MerchantResponse::printInvalidTransactionStatusResponse(
        $transaction_status,
        $order_id,
        $namespace
      );

      return;
    }

    // Handle success order
    // -----
    $order->update_status($this->get_option('default_order_success_status'));

    $order->payment_complete();

    MerchantResponse::printSuccessResponse($namespace);

    return;
  }
}
