{*
 * Form to be displayed in the payment step
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

  .payment-disabled {
    opacity: 0.5;
  }

  .payment-disabled label{
    text-align: left;
  }
</style>

{if $totalAmount < 10000 || $totalAmount > 25000000}
  <script>
    // disable payment method when total amount less than 10000 or more than 25000000
    document.addEventListener("DOMContentLoaded", function() {
      var input = document.querySelector('[data-module-name="{$displayName}"]');
      var paymentOption = input.closest('.payment-option');

      input.setAttribute('disabled', '');
      paymentOption.classList.add('payment-disabled');
      // display message
      paymentOption.querySelector('label').innerHTML += '<br><span style="float: left; font-size: 0.8rem;">Nilai transaksi Anda tidak sesuai dengan ketentuan penggunaan Indodana Paylater<\/span>';
    });
  </script>
{/if}
