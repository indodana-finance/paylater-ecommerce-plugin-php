{*
 * PrestaPay - A Sample Payment Module for PrestaShop 1.7
 *
 * Form to be displayed in the payment step
 *
 * @author Andresa Martins <contact@andresa.dev>
 * @license https://opensource.org/licenses/afl-3.0.php
 *}

<form method="post" action="{$action}" style="padding-left: 2.5rem;">
  <p>Pay with installment via our Paylater product.</p>
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
        {$currency.sign}{$option.monthlyInstallment|number_format:2:".":","}/bulan
      </label>
    </div>
  {/foreach}
</form>
