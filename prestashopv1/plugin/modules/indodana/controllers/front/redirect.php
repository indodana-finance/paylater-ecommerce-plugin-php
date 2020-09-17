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

class IndodanaRedirectModuleFrontController extends ModuleFrontController
{
  /**
   * Do whatever you have to before redirecting the customer on the website of your payment page.
   */
  public function postProcess()
  {
    /**
     * Oops, an error occured.
     */
    if (Tools::getValue('action') == 'error') {
      return $this->displayError('An error occurred while trying to redirect the customer');
    }

    $cart = $this->context->cart;

    $indodanaTools = new IndodanaTools();
    $installmentOptions = $indodanaTools->getIndodanaCommon()->getInstallmentOptions([
      'totalAmount' => $indodanaTools->getTotalAmount($cart),
      'discountAmount' => $indodanaTools->getTotalDiscountAmount($cart),
      'shippingAmount' => $indodanaTools->getTotalShippingAmount($cart),
      'taxAmount' => $indodanaTools->getTotalTaxAmount($cart),
      'products' => $indodanaTools->getProducts($cart),
      'adminFeeAmount' => $indodanaTools->getAdminFeeAmount($cart),
      'additionalFeeAmount' => $indodanaTools->getAdditionalFeeAmount($cart),
      'insuranceFeeAmount' => $indodanaTools->getInsuranceFeeAmount($cart)
    ]);

    $this->context->smarty->assign([
      'moduleName' => $this->module->name,
      'displayName' => $this->module->displayName,
      'indodanaLogo' => IndodanaCommon\IndodanaConstant::LOGO_URL,
      'cartId' => Context::getContext()->cart->id,
      'secureKey' => Context::getContext()->customer->secure_key,
      'nbProducts' => $cart->nbProducts(),
      'custCurrency' => $cart->id_currency,
      'currencies' => $this->module->getCurrency((int)$cart->id_currency),
      'installmentOptions' => $installmentOptions,
      'total' => $cart->getOrderTotal(true, Cart::BOTH),
      'this_path' => $this->module->getPathUri(),
      'this_path_bw' => $this->module->getPathUri(),
    ]);

    return $this->setTemplate('redirect.tpl');
  }

  protected function displayError($message, $description = false)
  {
    /**
     * Create the breadcrumb for your ModuleFrontController.
     */
    $this->context->smarty->assign(
      'path',
      '<a href="' . $this->context->link->getPageLink('order', null, null, 'step=3') . '">' . $this->module->l('Payment') . '</a>
			<span class="navigation-pipe">&gt;</span>' . $this->module->l('Error')
    );

    /**
     * Set error message and description for the template.
     */
    array_push($this->errors, $this->module->l($message), $description);

    return $this->setTemplate('error.tpl');
  }
}
