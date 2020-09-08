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

class IndodanaConfirmationModuleFrontController extends ModuleFrontController
{
  public function postProcess()
  {
    if ((Tools::isSubmit('cart_id') == false) || (Tools::isSubmit('secure_key') == false)) {
      return false;
    }

    $cartId = Tools::getValue('cart_id');
    $secureKey = Tools::getValue('secure_key');

    $cart = new Cart((int) $cartId);
    $customer = new Customer((int) $cart->id_customer);

    /**
     * Since it's an example we are validating the order right here,
     * You should not do it this way in your own module.
     */
    $paymentStatus = Configuration::get('PS_OS_PAYMENT'); // Default value for a payment that succeed.
    $message = null; // You can add a comment directly into the order so the merchant will see it in the BO.

    /**
     * Converting cart into a valid order
     */
    $moduleName = $this->module->displayName;
    $currencyId = (int) Context::getContext()->currency->id;

    $this->module->validateOrder(
      $cartId,
      $paymentStatus,
      $cart->getOrderTotal(),
      $moduleName,
      $message,
      array(),
      $currencyId,
      false,
      $secureKey
    );

    /**
     * If the order has been validated we try to retrieve it
     */
    $order_id = Order::getOrderByCartId((int) $cart->id);

    if ($order_id && ($secureKey == $customer->secure_key)) {
      /**
       * The order has been placed so we redirect the customer on the confirmation page.
       */
      $module_id = $this->module->id;
      Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cartId . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secureKey);
    } else {
      /**
       * An error occured and is shown on a new page.
       */
      $this->errors[] = $this->module->l('An error occured. Please contact the merchant to have more informations');

      return $this->setTemplate('error.tpl');
    }
  }
}
