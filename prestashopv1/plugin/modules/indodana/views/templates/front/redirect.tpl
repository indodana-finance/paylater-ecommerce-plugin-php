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

<div>
	{capture name=path}
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod="`$moduleName`"}">{l s='Checkout' mod="`$moduleName`"}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s="`$displayName` payment" mod="`$moduleName`"}
	{/capture}

	{* {include file="$tpl_dir./breadcrumb.tpl"} *}

	<h2 class="page-heading">{l s='Order summary' mod="`$moduleName`"}</h2>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{if $nbProducts <= 0}
		<p class="warning">{l s='Your shopping cart is empty.' mod="`$moduleName`"}</p>
	{else}

	<form action="{$link->getModuleLink($moduleName, 'validation', [], true)|escape:'html'}" method="post">
		<div class="box cheque-box">
			<h3 class="page-subheading">{l s="`$displayName` payment" mod="`$moduleName`"}</h3>
			<p class="cheque-indent">
                <strong class="dark">
					{* <img src="{$this_path_bw}logo.png" alt="{l s='Bank wire' mod="`$moduleName`"}" style="float:left; margin: 0px 10px 5px 0px;" /> *}
                    {l s="Pay with installment via our Paylater product." mod="`$moduleName`"}
                </strong>
            </p>
			{foreach from=$installmentOptions item=option}
				<div class="form-check">
				<input
					class="form-check-input"
					type="radio"
					name="indodana_selection"
					id="indodana_selection_{$option.id}"
					value="{$option.id}"
					style="margin-left: 0;"
					required>
				<label class="form-check-label" for="indodana_selection_{$option.id}" style="padding-left: 1.5rem;">
					{$option.paymentType}
					&emsp;|&emsp;
					{$currencies.0.sign}{$option.monthlyInstallment|number_format:2:".":","}/bulan
				</label>
				</div>
			{/foreach}
			<br>
			<p class="cheque-indent">
                <strong class="dark">
					{l s='Here is a short summary of your order:' mod="`$moduleName`"}
                </strong>
            </p>
			<p>
				- {l s='The total amount of your order is' mod="`$moduleName`"}
				<span id="amount" class="price">{displayPrice price=$total}</span>
				{if $use_taxes == 1}
					{l s='(tax incl.)' mod="`$moduleName`"}
				{/if}
			</p>
			{* <p>
				-
				{if $currencies|@count > 1}
					{l s='We allow several currencies to be sent via bank wire.' mod="`$moduleName`"}
					<br /><br />
					{l s='Choose one of the following:' mod="`$moduleName`"}
					<select id="currency_payement" name="currency_payement" onchange="setCurrency($('#currency_payement').val());">
						{foreach from=$currencies item=currency}
							<option value="{$currency.id_currency}" {if $currency.id_currency == $custCurrency}selected="selected"{/if}>{$currency.name}</option>
						{/foreach}
					</select>
				{else}
					{l s='We allow the following currency to be sent via bank wire:' mod="`$moduleName`"}&nbsp;<b>{$currencies.0.name}</b>
					<input type="hidden" name="currency_payement" value="{$currencies.0.id_currency}" />
				{/if}
			</p> *}
			<p>
				- {l s='Please confirm your order by clicking "I confirm my order".' mod="`$moduleName`"}
			</p>
			<p>
				- {l s='You will be redirected to indodana payment page' mod="`$moduleName`"}
			</p>
		</div>
		<p class="cart_navigation clearfix" id="cart_navigation">
			<a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}">
				<i class="icon-chevron-left"></i> {l s='Other payment methods' mod="`$moduleName`"}
			</a>
			<button class="button btn btn-default button-medium" type="submit">
				<span>{l s='I confirm my order' mod="`$moduleName`"} <i class="icon-chevron-right right"></i></span>
			</button>
		</p>
	</form>
	{/if}
</div>
