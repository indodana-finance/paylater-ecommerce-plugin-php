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

	<h2 class="page-heading">{l s='Order summary' mod="`$moduleName`"}</h2>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{if $nbProducts <= 0}
		<p class="warning">{l s='Your shopping cart is empty.' mod="`$moduleName`"}</p>
	{else}

	<form action="{$link->getModuleLink($moduleName, 'validation', [], true)|escape:'html'}" method="post">
		<div class="box cheque-box">
			<h3 class="page-subheading">
        <img src="{$indodanaLogo}" alt="{l s='Bank wire' mod="`$moduleName`"}">
      </h3>
			<p class="cheque-indent">
        <strong class="dark">
          {l s=$description mod="`$moduleName`"}
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
          <label class="form-check-label label-type font-weight-normal" for="indodana_selection_{$option.id}">
            {$option.paymentType}
          </label>
          <div class="filler"></div>
          <label class="form-check-label label-amount font-weight-normal" for="indodana_selection_{$option.id}">
            {displayPrice price=$option.monthlyInstallment}/bulan
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

<style>
  .form-indodana {
    padding-left: 2.5rem;
    padding-right: 0.5rem;
    background: #F6F6F6;
  }

  .indodana-payment-option {
    display: flex;
    flex-direction: column;
  }

  .form-check {
    display: flex;
  }

  .form-check-input:checked ~ label {
    font-weight: bold;
  }

  .radio {
    margin-top: 4px !important;
  }

  .filler {
    flex-grow: 1;
  }

  .label-type {
    padding-left: 1.5rem;
    text-align: left;
  }

  .font-weight-normal {
    font-weight: normal;
  }

  @media (max-width: 575.98px) {
    .form-check {
      width: 100%;
    }
  }

  @media (min-width: 576px) and (max-width: 767.98px) {
    .form-check {
      width: 100%;
    }
  }

  @media (min-width: 768px) and (max-width: 991.98px) {
    .form-check {
      width: 100%;
    }
  }

  @media (min-width: 992px) and (max-width: 1199.98px) {
    .form-check {
      width: 80%;
    }
  }

  @media (min-width: 1200px) {
    .form-check {
      width: 65%;
    }
  }
</style>

<script>
$(function () {
  // bold selected payment text
  $('input[name="indodana_selection"]').change(function () {
    $('.form-check').find('label').addClass('font-weight-normal');
    $(this).parents('.form-check').find('label').removeClass('font-weight-normal');
  });
})
</script>
