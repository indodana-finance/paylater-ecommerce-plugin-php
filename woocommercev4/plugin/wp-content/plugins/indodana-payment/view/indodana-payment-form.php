<div id='content'>
  <div class='checkout-product'>
    <?php foreach($paymentOptions as $paymentOption) { ?>
      <label class='payment-option'>
        <input type='radio' name='payment_selection' class='checkbox-default' value='<?=$paymentOption['id'] ?>'>
        <span class="checkbox checkbox-styled"></span>
        <p class="installment-type"><?=$paymentOption['paymentType']; ?></p>
        <div class="filler"></div>
        <p class="installment-amount"><?= $paymentOption['monthlyInstallment'] . '/bulan'; ?></p>
      </label>
    <?php } ?>
  </div>

  <style>
    .checkbox-default {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    .checkbox-default:checked ~ .checkbox {
      background-color: #3db3b3;
    }

    .checkbox-default:checked ~ .checkbox::after {
      content: '';
      display: block;
      margin-left: 4px;
      width: 4px;
      height: 8px;
      border: solid #ffffff;
      border-width: 0 2px 2px 0;
      -webkit-transform: rotate(45deg);
      -ms-transform: rotate(45deg);
      -webkit-transform: rotate(45deg);
      -ms-transform: rotate(45deg);
      transform: rotate(45deg);
    }

    .installment-type {
      font-size: 14px;
    }

    .installment-amount {
      font-size: 16px;
    }

    .checkbox-default:checked ~ p {
      font-weight: bold;
    }

    .checkbox-styled {
      height: 15px;
      width: 15px;
      min-width: 15px;
      margin-top: 4px;
      border-radius: 50%;
      margin-right: 5px;
      background-color: #ffffff;
      border: 1px solid #3db3b3;
    }

    .filler {
      flex-grow: 1;
    }

    .payment-option > * {
      display: inline-block;"

    }

    .payment-option {
      width: 100%;
      display: flex;
      padding: 0.5em 0 0.5em 0;
    }

    .confirm-button {
      background: #FFae31;
      color: #573a10;
      border: none;
      font-size: 15px;
      line-height: 18px;
      border-radius: 22.5px;
      padding: 8px 24px;
      min-height: 40px;
      height: auto;
      cursor: pointer;
      -webkit-transition: opacity .375s ease;
      transition: opacity .375s ease;
      text-transform: uppercase;
      font-weight: bold;
      margin: 0.5rem 0;
    }

    .indodana-header-image {
      max-width: 40%;
    }

    .checkout-product {
      padding: 1em 0.5em 1em 0.5em;
      overflow: hidden;
    }
  </style>
</div>
