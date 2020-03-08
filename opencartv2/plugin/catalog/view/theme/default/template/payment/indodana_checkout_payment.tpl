<div>
  <h2><?=$title ?></h2>
  <input type='hidden' value='<?=$orderData; ?>' id='orderData'/>
  <input type='hidden' value='<?=$authorization; ?>' id='authorization'/>
  <input type='hidden' value='<?=$merchantConfirmPaymentUrl; ?>' id='merchantConfirmPaymentUrl' />
  <input type='hidden' value='<?=$indodanaBaseUrl; ?>' id='indodanaBaseUrl' />
  <div class='table-responsive'>
    <table class="table table-bordered table-hover">
      <thead><tr>
        <td></td>
        <td><?=$text_payment_options_name ?></td>
        <td><?=$text_payment_options_monthly_installment ?></td>
        <td><?=$text_payment_options_total_amount ?></td>
      </tr></thead>
      <tbody>
        <?php foreach($paymentOptions as $paymentOption) { ?>
          <tr>
            <td><input type='radio' name='paymentSelection' value='<?=$paymentOption['id'] ?>'></td>
            <td><?=$paymentOption['paymentType']; ?></td>
            <td><?=$paymentOption['monthlyInstallment']; ?></td>
            <td><?=$paymentOption['installmentAmount']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <div class='buttons'>
      <div class="pull-right">
        <input type='button' value='<?=$text_button_confirm; ?>' id='confirmButton' class='btn btn-primary'/>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  $('#confirmButton').click(function() {
    var jsonData = $('#orderData').val();
    var data = JSON.parse(jsonData);
    var paymentOptionId = $("input[name='paymentSelection']:checked").val();
    confirmPayment(data.transactionDetails.merchantOrderId, function() {
      redirectToCheckoutUrl(paymentOptionId, data);
    });
  })
});

function confirmPayment(orderId, success) {
  var confirmPaymentUrl = $('#merchantConfirmPaymentUrl').val();
  var data = {
    orderId: orderId
  }
  $.ajax({
    url: confirmPaymentUrl,
    type: 'post',
    data: JSON.stringify(data),
    headers: {
      'Content-type': 'application/json',
      'Accept': 'application/json'
    },
    dataType: 'json',
    success: success
  });
}

function getAuthorizationHeader() {
  var authorization = $('#authorization').val();
  return authorization;
}

function redirectToCheckoutUrl(paymentOptionId, paymentData) {
  var data = paymentData;
  var baseUrl = $('#indodanaBaseUrl').val();
  data.paymentType = paymentOptionId;
  $.ajax({
    url: `${baseUrl}/merchant/v1/checkout_url`,
    type: 'post',
    data: JSON.stringify(data),
    headers: {
      'Content-type': 'application/json',
      'Accept': 'application/json',
      'Authorization': getAuthorizationHeader()
    },
    dataType: 'json',
    success: function(data) {
      const redirectUrl = data.redirectUrl;
      window.location = redirectUrl;
    }
  });
}
