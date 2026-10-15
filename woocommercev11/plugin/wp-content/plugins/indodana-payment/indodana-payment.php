<?php
/*
 * Plugin Name: Indodana Payment
 * Plugin URI: https://indodana.com
 * Description: "Paylater" service registered on OJK.
 * Author: Indodana
 * Author URI: https://indodana.com
 * Version: 2.0.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 11.2
 */

define('INDODANA_PLUGIN_FILE', __FILE__);

add_filter( 'woocommerce_payment_gateways', 'indodana_add_payment_gateway_class' );
function indodana_add_payment_gateway_class( $gateways ) {
  $gateways[] = 'WC_Indodana_Gateway';
  return $gateways;
}

add_action( 'plugins_loaded', 'indodana_init_payment_gateway_class' );
function indodana_init_payment_gateway_class() {
  if (!class_exists('WC_Payment_Gateway')) {
    return;
  }

  require_once 'library/Indodana/Payment/autoload.php';
  require_once 'view/renderer.php';
  require_once 'indodana-payment-gateway.php';
}

/**
 * Declare compatibility with WooCommerce features that are opt-in.
 *
 * - `custom_order_tables`: High-Performance Order Storage (HPOS). The gateway only
 *   ever reads/writes orders through `wc_get_order()` and `WC_Order` CRUD methods,
 *   so it works the same whether orders are stored in posts or in the dedicated tables.
 * - `cart_checkout_blocks`: Cart & Checkout blocks. See `WC_Indodana_Blocks` below.
 */
add_action( 'before_woocommerce_init', 'indodana_declare_woocommerce_compatibility' );
function indodana_declare_woocommerce_compatibility() {
  if (!class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    return;
  }

  \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
    'custom_order_tables',
    INDODANA_PLUGIN_FILE,
    true
  );

  \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
    'cart_checkout_blocks',
    INDODANA_PLUGIN_FILE,
    true
  );
}

/**
 * Register the Indodana payment method with the Cart & Checkout blocks (Store API).
 */
add_action( 'woocommerce_blocks_loaded', 'indodana_register_blocks_payment_method' );
function indodana_register_blocks_payment_method() {
  if (!class_exists('\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
    return;
  }

  require_once 'library/Indodana/Payment/autoload.php';
  require_once 'includes/class-wc-indodana-blocks.php';

  add_action(
    'woocommerce_blocks_payment_method_type_registration',
    function( \Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
      $payment_method_registry->register( new WC_Indodana_Blocks() );
    }
  );
}
