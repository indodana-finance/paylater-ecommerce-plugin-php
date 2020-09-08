{*
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
*}

<p class="payment_module" id="indodana_payment_button">
  <a href="{$link->getModuleLink($moduleName, 'redirect', array(), true)|escape:'htmlall':'UTF-8'}" class="payment-method" title="{l s='Pay with ' mod="`$moduleName`"}  {$displayName}">
    <img src="{$indodanaLogo|escape:'htmlall':'UTF-8'}" alt="{l s='Pay with my payment module' mod="`$moduleName`"}" />
    <span class="payment-text">
      {l s='Pay with ' mod="`$moduleName`"} {$displayName}
    </span>
  </a>
</p>

<style>
.payment-method {
  display: flex !important;
  flex-direction: row;
  align-items: center;
}

.payment-text {
  margin-left: 1rem;
}
</style>
