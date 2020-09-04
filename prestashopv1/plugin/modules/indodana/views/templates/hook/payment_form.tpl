{*
 * PrestaPay - A Sample Payment Module for PrestaShop 1.7
 *
 * Form to be displayed in the payment step
 *
 * @author Andresa Martins <contact@andresa.dev>
 * @license https://opensource.org/licenses/afl-3.0.php
 *}

<form method="post" action="{$action}" class="form-indodana">
  <p>Pay with installment via our Paylater product.</p>
  <div class="indodana-payment-option">
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
        <label class="form-check-label label-type" for="indodana_selection_{$option.id}">
          {$option.paymentType}
        </label>
        <div class="filler"></div>
        <label class="form-check-label label-amount" for="indodana_selection_{$option.id}">
          {$currency.sign}{$option.monthlyInstallment|number_format:2:".":","}/bulan
        </label>
      </div>
    {/foreach}
  </div>
</form>


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

  .filler {
    flex-grow: 1;
  }

  .label-type {
    padding-left: 1.5rem;
    text-align: left;
  }
</style>
