<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\IndodanaLogger;

/**
 * Registers Indodana as a payment method for the Cart & Checkout blocks.
 *
 * This mirrors `WC_Indodana_Gateway::payment_fields()` for the classic checkout:
 * it reads the same settings (`woocommerce_indodana_settings`) and computes the
 * same installment options from the current cart, but renders them through the
 * Store API / block checkout instead of `woocommerce_checkout` template hooks.
 */
final class WC_Indodana_Blocks extends AbstractPaymentMethodType
{
  protected $name = 'indodana';

  public function initialize()
  {
    $this->settings = get_option('woocommerce_indodana_settings', []);
  }

  public function is_active()
  {
    return isset($this->settings['enabled']) && 'yes' === $this->settings['enabled'];
  }

  public function get_payment_method_script_handles()
  {
    wp_register_script(
      'wc-indodana-blocks',
      plugins_url('assets/js/indodana-blocks.js', INDODANA_PLUGIN_FILE),
      [ 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities', 'wp-i18n' ],
      filemtime(plugin_dir_path(INDODANA_PLUGIN_FILE) . 'assets/js/indodana-blocks.js'),
      true
    );

    wp_set_script_translations('wc-indodana-blocks', 'indodana-blocks');

    return [ 'wc-indodana-blocks' ];
  }

  public function get_payment_method_data()
  {
    return [
      'title'             => $this->settings['title'] ?? 'Indodana PayLater',
      'description'       => $this->settings['description'] ?? '',
      'icon'              => IndodanaConstant::LOGO_URL,
      'installmentOptions' => $this->get_installment_options(),
    ];
  }

  /**
   * Installment options for the current cart, computed the same way as
   * `WC_Indodana_Gateway::payment_fields()`.
   *
   * @return array
   */
  private function get_installment_options()
  {
    if (!function_exists('WC') || !WC()->cart || !class_exists('WC_Indodana_Gateway')) {
      return [];
    }

    $gateways = WC()->payment_gateways() ? WC()->payment_gateways()->payment_gateways() : [];

    if (!isset($gateways[$this->name]) || !$gateways[$this->name]->is_available()) {
      return [];
    }

    /** @var WC_Indodana_Gateway $gateway */
    $gateway = $gateways[$this->name];
    $cart = WC()->cart;

    try {
      return $gateway->getInstallmentOptionsForCart($cart);
    } catch (\Throwable $exception) {
      IndodanaLogger::error(sprintf(
        '[Woocommerce-Blocks-get_installment_options] Failed to get installment options: %s',
        $exception->getMessage()
      ));

      return [];
    }
  }
}
