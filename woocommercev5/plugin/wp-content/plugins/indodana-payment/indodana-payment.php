<?php
/*
 * Plugin Name: Indodana Payment
 * Plugin URI: https://indodana.com
 * Description: "Paylater" service registered on OJK.
 * Author: Indodana
 * Author URI: https://indodana.com
 * Version: 1.0.0
 */

use IndodanaCommon\IndodanaConstant;

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
