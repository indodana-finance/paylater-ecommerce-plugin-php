<?php

require_once INDODANA_PLUGIN_ROOT_DIR . 'autoload.php';

use Respect\Validation\Validator;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaInterface;
use IndodanaCommon\IndodanaHelper;
use IndodanaCommon\IndodanaLogger;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\MerchantResponse;

/**
 * Contains Indodana functionalities for both admin and customer.
 */
class WC_Indodana_Gateway extends WC_Payment_Gateway implements IndodanaInterface
{
  private $indodana_common;

  public function __construct() {
    $this->id = 'indodana';
    $this->has_fields = true;

    $this->method_title = __('Indodana PayLater', 'indodana-method-title');
    $this->method_description = __('Indodana PayLater redirects customers to Indodana during checkout.', 'indodana-method-description');

    $this->supports = array(
      'products'
    );

    $this->init_form_fields();
    $this->init_settings();

    $this->title = $this->get_option('title');
    $this->description = $this->get_option('description');
    $this->icon = IndodanaConstant::LOGO_URL;

    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
    add_action('woocommerce_api_wc_indodana_gateway', array($this, 'indodana_callback'));
  }

  /**
   * Getter for indodana common.
   *
   * We decide to "lazily" load indodana common because `get_option` might cause bug if we load this "eagerly".
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
   * Initialize form fields for admin settings.
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
        'type'            => 'password',
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
   * Used to save and validate input on Indodana Configuration.
   *
   * Override from `wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-settings-api.php` with minor tweaks.
   *
   * @return bool is anything saved?
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

      // Same guard as the WooCommerce core: `title` fields are headings, not settings
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
    return (float) $cart->get_total('edit');
  }

  public function getTotalDiscountAmount($cart)
  {
    return (float) $cart->get_discount_total();
  }

  public function getTotalShippingAmount($cart)
  {
    return (float) $cart->get_shipping_total();
  }

  public function getTotalTaxAmount($cart)
  {
    return (float) $cart->get_total_tax();
  }

  /**
   * Total admin fee amount for Indodana Transaction.
   *
   * Because we didn't have the value for this variable (at least to our understanding, no),
   * We intentionally hardcode it to 0 so that we're aware of existance and usage of this variable.
   *
   * @param Cart $cart the cart
   * @return double
   */
  public function getTotalAdminFeeAmount($cart)
  {
    return (float) 0;
  }

  /**
   * Total additional fee amount for Indodana Transaction.
   *
   * @param Cart $cart the cart
   * @return float
   */
  public function getTotalAdditionalFeeAmount($cart)
  {
    return (float) $cart->get_fee_total();
  }

  /**
   * Total insurance fee amount for Indodana Transaction.
   *
   * Because we didn't have the value for this variable (at least to our understanding, no),
   * We intentionally hardcode it to 0 so that we're aware of existance and usage of this variable.
   *
   * @param Cart $cart the cart
   * @return double
   */
  public function getTotalInsuranceFeeAmount($cart)
  {
    return (float) 0;
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
        // We will use DEFAULT_ITEM_CATEGORY atm until we found a way to get specific plugin category mapping
        'category'  => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
        'quantity' => (int) $cart_item['quantity'],
      ];
    }

    return $products;
  }

  /**
   * Amounts for the Indodana checkout request, built from the `WC_Order`.
   *
   * Unlike `getTotal*($cart)` above (used to render installment options before the
   * order exists), these are read from order CRUD methods so they stay correct
   * whether the order was placed through the classic or the block checkout.
   *
   * @param WC_Order $order the order
   * @return array
   */
  public function getOrderAmounts($order)
  {
    return [
      'totalAmount'         => (float) $order->get_total(),
      'shippingAmount'      => (float) $order->get_shipping_total(),
      'taxAmount'           => (float) $order->get_total_tax(),
      'discountAmount'      => (float) $order->get_discount_total(),
      // Hardcoded to 0, same as getTotalAdminFeeAmount($cart) above.
      'adminFeeAmount'      => (float) 0,
      'additionalFeeAmount' => (float) $order->get_total_fees(),
      // Hardcoded to 0, same as getTotalInsuranceFeeAmount($cart) above.
      'insuranceFeeAmount'  => (float) 0,
    ];
  }

  /**
   * Products for the Indodana checkout request, built from the `WC_Order` line items.
   *
   * @param WC_Order $order the order
   * @return array
   */
  public function getProductsFromOrder($order)
  {
    $products = [];

    foreach ($order->get_items() as $item) {
      $product = is_callable([$item, 'get_product']) ? $item->get_product() : false;

      $image_url = ($product && !empty($product->get_image_id())) ?
        wp_get_attachment_image_url($product->get_image_id(), 'full') :
        '';

      $type = $product ? ($product->get_type() ?? '') : '';
      $url = $product ? get_permalink($product->get_id()) : '';

      $quantity = (int) $item->get_quantity();
      // Post-discount line total (matches the order total after coupons), rounded to the currency decimals
      $line_total = (float) $item->get_total();
      $unit_price = round($quantity > 0 ? ($line_total / $quantity) : $line_total, wc_get_price_decimals());
      // Same id as the cart side (`$product->get_id()`): the variation id when there is one
      $product_id = $item->get_variation_id() ?: $item->get_product_id();

      $products[] = [
        'id' => (string) $product_id,
        'name' => $item->get_name(),
        'price' => (float) $unit_price,
        'url' => $url,
        'imageUrl' => $image_url,
        'type' => $type,
        // We will use DEFAULT_ITEM_CATEGORY atm until we found a way to get specific plugin category mapping
        'category'  => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
        'quantity' => $quantity,
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

  /**
   * Installment options for `$cart`, shared by the classic checkout (`payment_fields()`)
   * and the Cart & Checkout blocks (`WC_Indodana_Blocks`).
   *
   * @param WC_Cart $cart the cart
   * @return array
   */
  public function getInstallmentOptionsForCart($cart) {
    return $this->get_indodana_common()->getInstallmentOptions([
      'totalAmount'         => $this->getTotalAmount($cart),
      'shippingAmount'      => $this->getTotalShippingAmount($cart),
      'taxAmount'           => $this->getTotalTaxAmount($cart),
      'discountAmount'      => $this->getTotalDiscountAmount($cart),
      'adminFeeAmount'      => $this->getTotalAdminFeeAmount($cart),
      'additionalFeeAmount' => $this->getTotalAdditionalFeeAmount($cart),
      'insuranceFeeAmount'  => $this->getTotalInsuranceFeeAmount($cart),
      'products'            => $this->getProducts($cart)
    ]);
  }

  /**
   * Display Indodana payment options on checkout page.
   */
  public function payment_fields() {
    echo wpautop(wp_kses_post($this->description));

    $cart = WC()->cart;

    $payment_options = $this->getInstallmentOptionsForCart($cart);

    $data = [];
    $data['paymentOptions'] = $payment_options;

    do_action('woocommerce_credit_card_form_start', $this->id);

    echo Renderer::render(plugin_dir_path(INDODANA_PLUGIN_FILE) . 'view/indodana-payment-form.php', $data);

    do_action('woocommerce_credit_card_form_end', $this->id);
  }

  /**
   * Proceed checkout to Indodana.
   */
  public function process_payment($order_id) {
    $namespace = '[Woocommerce-process_payment]';

    $order = wc_get_order($order_id);

    if (!$order) {
      wc_add_notice(__('Unable to find the order to process the Indodana payment.', 'indodana-process-payment'), 'error');

      return [ 'result' => 'failure' ];
    }

    $payment_selection = isset($_POST['payment_selection']) ?
      sanitize_text_field(wp_unslash($_POST['payment_selection'])) :
      '';

    // Never log the whole `$_POST`, it contains customer personal data
    IndodanaLogger::info(
      sprintf(
        '%s Order id: %s, payment selection: %s',
        $namespace,
        $order_id,
        $payment_selection
      )
    );

    if ($payment_selection === '') {
      wc_add_notice(__('Please select an Indodana installment option before continuing.', 'indodana-process-payment'), 'error');

      return [ 'result' => 'failure' ];
    }

    try {
      // Make sure the selection is one of the options we actually offer for this cart
      $offered_option_ids = array_map(
        function ($option) {
          return (string) $option['id'];
        },
        WC()->cart ? $this->getInstallmentOptionsForCart(WC()->cart) : []
      );

      if (!in_array($payment_selection, $offered_option_ids, true)) {
        wc_add_notice(__('The selected Indodana installment option is not available. Please select another option.', 'indodana-process-payment'), 'error');

        return [ 'result' => 'failure' ];
      }

      $order_key = $order->get_order_key();

      $approved_notification_url = add_query_arg(array(
        'wc-api'    => 'WC_Indodana_Gateway',
      ), home_url('/'));

      $cancellation_redirect_url = add_query_arg(array(
        'wc-api'    => 'WC_Indodana_Gateway',
        'method'    => 'cancel',
        'order_id'  => $order_id,
        'key'       => $order_key
      ), home_url('/'));

      $back_to_store_url = add_query_arg(array(
        'wc-api'    => 'WC_Indodana_Gateway',
        'method'    => 'complete',
        'order_id'  => $order_id,
        'key'       => $order_key
      ), home_url('/'));

      $checkout_url = $this->get_indodana_common()->checkout(array_merge(
        [
          'merchantOrderId' => $order_id,
        ],
        $this->getOrderAmounts($order),
        [
          'products'                => $this->getProductsFromOrder($order),
          'customerDetails'         => $this->getCustomerDetails($order),
          'billingAddress'          => $this->getBillingAddress($order),
          'shippingAddress'         => $this->getShippingAddress($order),
          'paymentType'             => $payment_selection,
          'approvedNotificationUrl' => $approved_notification_url,
          'cancellationRedirectUrl' => $cancellation_redirect_url,
          'backToStoreUrl'          => $back_to_store_url
        ]
      ));
    } catch (\Throwable $exception) {
      IndodanaLogger::error(sprintf(
        '%s Failed to process payment for order %s: %s',
        $namespace,
        $order_id,
        $exception->getMessage()
      ));

      wc_add_notice($exception->getMessage(), 'error');

      return [ 'result' => 'failure' ];
    }

    $order->update_status($this->get_option('default_order_pending_status'));

    // The cart is intentionally not emptied here: WooCommerce empties it once the
    // customer lands on the order received page, so cancelling at Indodana keeps it.

    return [
      'result' => 'success',
      'redirect' => $checkout_url
    ];
  }

  /**
   * Every request with `wc-api=WC_INDODANA_Gateway` will go through this function.
   */
  public function indodana_callback() {
    // Approve notification url
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->handle_notify_action();

      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!isset($_GET['method']) || !isset($_GET['order_id'])) {
        return;
      }

      $method = sanitize_key($_GET['method']);
      $order_id = absint($_GET['order_id']);

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

  /**
   * Whether `$order` exists and was paid for through this gateway.
   *
   * @param WC_Order|false $order the order
   * @return bool
   */
  private function is_valid_indodana_order($order) {
    return $order && $order->get_payment_method() === $this->id;
  }

  /**
   * Whether the `key` query param matches the order key of `$order`.
   *
   * The `wc-api` endpoint is public and order ids are sequential, so the order key
   * (known only to the customer and Indodana) is what proves the caller owns the order.
   *
   * @param WC_Order $order the order
   * @return bool
   */
  private function has_valid_order_key($order) {
    $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';

    return $key !== '' && hash_equals($order->get_order_key(), $key);
  }

  /**
   * Handler when the customer cancel the order.
   */
  private function handle_cancel_action($order_id) {
    $order = wc_get_order($order_id);

    if (!$this->is_valid_indodana_order($order) || !$this->has_valid_order_key($order)) {
      wp_redirect(home_url('/'));
      exit();
    }

    // Only unpaid orders can be cancelled, never flip a processing/completed one
    if ($order->has_status(['pending', 'on-hold'])) {
      $order->update_status($this->get_option('default_order_failed_status'));
    }

    wp_redirect($order->get_cancel_order_url());
    exit();
  }

  /**
   * Handler when the customer finish payment of the order using Indodana.
   */
  private function handle_complete_action($order_id) {
    $order = wc_get_order($order_id);

    if (!$this->is_valid_indodana_order($order) || !$this->has_valid_order_key($order)) {
      wp_redirect(home_url('/'));
      exit();
    }

    wp_redirect($order->get_checkout_order_received_url());
    exit();
  }

  /**
   * Handler when the Indodana notify the successfull order.
   */
  private function handle_notify_action() {
    // Log request headers
    // -----
    $namespace = '[Woocommerce-handle_notify_action]';

    $request_headers = IndodanaHelper::getRequestHeaders();

    // Never log credentials
    $loggable_headers = array_diff_key(
      $request_headers,
      array_flip([ 'Authorization', 'X-Signature' ])
    );

    IndodanaLogger::info(
      sprintf(
        '%s Request headers: %s',
        $namespace,
        json_encode($loggable_headers)
      )
    );

    // Check whether request authorization is valid
    // -----
    $auth_token = IndodanaHelper::getAuthToken($request_headers, $namespace);

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
    $order_id = absint($request_body['merchantOrderId']);

    $order = wc_get_order($order_id);

    if (!$this->is_valid_indodana_order($order)) {
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
    // Indodana may retry the notification, don't process an already paid order twice
    if ($order->is_paid()) {
      MerchantResponse::printSuccessResponse($namespace);

      return;
    }

    $order->payment_complete();

    // `payment_complete()` already moved the order to its own status,
    // only apply the configured one when it differs
    $success_status = str_replace('wc-', '', $this->get_option('default_order_success_status'));

    if ($success_status !== '' && !$order->has_status($success_status)) {
      $order->update_status($success_status);
    }

    MerchantResponse::printSuccessResponse($namespace);

    return;
  }
}
