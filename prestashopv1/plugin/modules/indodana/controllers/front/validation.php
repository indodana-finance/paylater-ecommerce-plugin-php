<?php

// ignore vscode's phpcs extension missing namespace error
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once(_PS_MODULE_DIR_ . 'indodana' . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'IndodanaTools.php');

class IndodanaValidationModuleFrontController extends ModuleFrontController
{
  /**
   * This class should be use by your Instant Payment
   * Notification system to validate the order remotely
   */
  public function postProcess()
  {
    /**
     * If the module is not active anymore, no need to process anything.
     */
    $cart = $this->context->cart;

    if (
      $cart->id_customer == 0
      || $cart->id_address_delivery == 0
      || $cart->id_address_invoice == 0
      || !$this->module->active
      || empty($_POST['indodana_selection'])
    ) {
      Tools::redirect('index.php?controller=order&step=1');
    }

    // Check that this payment option is still available in case the
    // customer changed his address just before the end of the checkout process
    $authorized = false;
    foreach (Module::getPaymentModules() as $module) {
      if ($module['name'] == 'indodana') {
        $authorized = true;
        break;
      }
    }
    if (!$authorized) {
      die($this->module->getTranslator()->trans('This payment method is not available.', [], 'Modules.Wirepayment.Shop'));
    }

    $customer = new Customer($cart->id_customer);
    if (!Validate::isLoadedObject($customer)) {
      Tools::redirect('index.php?controller=order&step=1');
    }

    $currency = $this->context->currency;
    $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

    $paymentStatus = Configuration::get('INDODANA_DEFAULT_ORDER_PENDING_STATUS');
    $message = null;

    $namespace = '[PrestashopV1-validateOrder]';
    $log = $this->getValidateOrderLogData($cart);

    IndodanaCommon\IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($log)
      )
    );

    /**
     * Create order
     */
    $this->module->validateOrder(
      $cart->id,
      $paymentStatus,
      $total,
      $this->module->displayName,
      $message,
      [],
      (int) $currency->id,
      false,
      $customer->secure_key
    );

    $orderId = Order::getOrderByCartId((int) $cart->id);
    $order = new Order((int) $orderId);

    /**
     * An error occured and is shown on a new page.
     */
    if (!$orderId) {
      $this->errors[] = $this->module->l('An error occured. Please contact the merchant to have more informations');

      return $this->setTemplate('error.tpl');
    }

    /**
     * The order has been placed so we redirect the customer on the indodana payment page.
     */
    $moduleId = $this->module->id;
    $approveUrl = $this->context->link->getModuleLink(
      $this->module->name,
      'approve',
      ['id_order' => $orderId],
      true
    );
    $cancelUrl = $this->context->link->getModuleLink(
      $this->module->name,
      'cancel',
      ['id_order' => $orderId],
      true
    );
    $backUrl = _PS_BASE_URL_ . '/index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $moduleId . '&id_order=' . $orderId . '&key=' . $customer->secure_key;

    $indodanaTools = new IndodanaTools();
    $redirectUrl = $indodanaTools->getIndodanaCommon()->checkout([
      'merchantOrderId' => $orderId . '-' . $order->reference,
      'totalAmount' => $indodanaTools->getTotalAmount($cart),
      'discountAmount' => $indodanaTools->getTotalDiscountAmount($cart),
      'shippingAmount' => $indodanaTools->getTotalShippingAmount($cart),
      'taxAmount' => $indodanaTools->getTotalTaxAmount($cart),
      'products' => $indodanaTools->getProducts($cart),
      'customerDetails' => $indodanaTools->getCustomerDetails($order),
      'billingAddress' => $indodanaTools->getBillingAddress($order),
      'shippingAddress' => $indodanaTools->getShippingAddress($order),
      'paymentType' => $_POST['indodana_selection'],
      'approvedNotificationUrl' => $approveUrl,
      'cancellationRedirectUrl' => $cancelUrl,
      'backToStoreUrl' => $backUrl
    ]);

    Tools::redirect($redirectUrl);
  }

  private function getValidateOrderLogData($cart)
  {
    $indodanaTools = new IndodanaTools();
    $billingAddress = $indodanaTools->getBillingAddress($cart);
    $payment = [
      'paymentMethod' => $this->module->name,
      'paymentSelection' => $_POST['indodana_selection'],
      'totalAmount' => $indodanaTools->getTotalAmount($cart)
    ];

    return array_merge($billingAddress, $payment);
  }
}
