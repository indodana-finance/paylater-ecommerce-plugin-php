<div id='content'>
  <h2><?=$textPaymentOptions ?></h2>
  <input type='hidden' value='<?=$orderData; ?>' id='orderData'/>
  <input type='hidden' value='<?=$authorization; ?>' id='authorization'/>
  <input type='hidden' value='<?=$merchantConfirmPaymentUrl; ?>' id='merchantConfirmPaymentUrl' />
  <input type='hidden' value='<?=$indodanaBaseUrl; ?>' id='indodanaBaseUrl' />
  <div class='checkout-product'>
    <table>
      <thead><tr>
        <td></td>
        <td><?=$textPaymentOptionsName ?></td>
        <td><?=$textPaymentOptionsMonthlyInstallment ?></td>
        <td><?=$textPaymentOptionsTotalAmount ?></td>
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
    <div class='right'>
      <input type='button' style='float: right;' value='<?=$textButtonConfirm; ?>' id='confirmButton' class='button'/>
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
  console.log(baseUrl);
  data.paymentType = paymentOptionId;
  $.ajax({
    url: `${baseUrl}/v1/checkout_url`,
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
</script>
