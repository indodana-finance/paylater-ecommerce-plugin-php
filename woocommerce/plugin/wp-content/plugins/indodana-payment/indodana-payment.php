<?php
/*
 * Plugin Name: Indodana Payment
 * Plugin URI: https://indodana.com
 * Description: "Paylater" service registered on OJK.
 * Author: Indodana
 * Author URI: https://indodana.com
 * Version: 1.2.0
 */

add_filter( 'woocommerce_payment_gateways', 'indodana_add_payment_gateway_class' );
function indodana_add_payment_gateway_class( $gateways ) {
  $gateways[] = 'WC_Indodana_Gateway';
  return $gateways;
}

add_filter('woocommerce_available_payment_gateways', 'indodana_add_available_payment_gateways');
function indodana_add_available_payment_gateways($available_gateways) {
  $available_gateways['indodana']->title = '<a onclick="window.open(\'http://indodana.com\');"><img src="https://afpi.or.id/fm/Members/indodana_logo_4500-x-1000.png" alt="Indodana Payments" title="Indodana Payments"/></a>';

  return $available_gateways;
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
