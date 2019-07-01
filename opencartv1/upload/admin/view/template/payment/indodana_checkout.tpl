<?=$header; ?>
<div id="content">
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment/indodana.png" alt="" /><?=$heading_title; ?></h1>
      <div class="buttons">
        <a onclick="$('#form').submit();" class="button">
          <?=$text_button_save; ?>
        </a>
        <a href="<?=$form_cancel; ?>" class="button">
          <?=$text_button_cancel; ?>
        </a>
      </div>
    </div>
    <div class="content">
      <form action="<?=$form_action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><h2>Merchant's Detail</h2></td>
          </tr>
          <tr>
            <td><?=$entry_first_name; ?></td>
            <td>
              <input type="text" name="indodana_checkout_first_name" value="<?=$indodana_checkout_first_name; ?>" size="30"/>
              <?php if ($error_first_name_empty) { ?>
                <span class="error"><?=$error_first_name_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_last_name; ?></td>
            <td><input type="text" name="indodana_checkout_last_name" value="<?=$indodana_checkout_last_name; ?>" size="30"/></td>
          </tr>
          <tr>
            <td><?=$entry_address; ?></td>
            <td>
              <textarea name="indodana_checkout_address" rows="5" cols="30"><?=$indodana_checkout_address; ?></textarea>
              <?php if ($error_address_empty) { ?>
                <span class="error"><?=$error_address_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_city; ?></td>
            <td>
              <input type="text" name="indodana_checkout_city" value="<?=$indodana_checkout_city; ?>" size="30"/>
              <?php if ($error_city_empty) { ?>
                <span class="error"><?=$error_city_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_postal_code; ?></td>
            <td>
              <input type="text" name="indodana_checkout_postal_code" value="<?=$indodana_checkout_postal_code; ?>" size="10"/>
              <?php if ($error_postal_code_empty) { ?>
                <span class="error"><?=$error_postal_code_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_phone; ?></td>
            <td>
              <input type="text" name="indodana_checkout_phone" value="<?=$indodana_checkout_phone; ?>" size="15"/>
              <?php if ($error_phone_empty) { ?>
                <span class="error"><?=$error_phone_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_country_code; ?></td>
            <td><select name="indodana_checkout_default_country_code">
              <?php foreach ($country_codes as $country_code) { ?>
                <?php if ($country_code == $indodana_checkout_default_country_code) { ?>
                  <option value="<?=$country_code; ?>" selected="selected"><?=$country_code; ?></option>
                <?php } else { ?>
                  <option value="<?=$country_code; ?>"><?=$country_code; ?></option>
                <?php } ?>
              <?php } ?>
              <?php if ($error_country_code_empty) { ?>
                <span class="error"><?=$error_country_code_empty; ?></span>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><h2>Indodana Paylater Configuration</h2></td>
          </tr>
          <tr>
            <td><?=$entry_api_secret; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_secret" value="<?=$indodana_checkout_api_secret; ?>" size="30"/>
              <?php if ($error_api_secret_empty) { ?>
                <span class="error"><?=$error_api_secret_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_api_key; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_key" value="<?=$indodana_checkout_api_key; ?>" size="30"/>
              <?php if ($error_api_key_empty) { ?>
                <span class="error"><?=$error_api_key_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_environment; ?></td>
            <td>
              <?php if ($indodana_checkout_environment == $environment_production) { ?>
                <label>
                  <input type="radio" name="indodana_checkout_environment" value="<?=$environment_sandbox; ?>"/>
                  <?=$text_environment_sandbox; ?>
                </label>
                <label>
                  <input type="radio" name="indodana_checkout_environment" value="<?=$environment_production; ?>" checked/>
                  <?=$text_environment_production; ?>
                </label>
              <?php } else { ?>
                <label>
                  <input type="radio" name="indodana_checkout_environment" value="<?=$environment_sandbox; ?>" checked/>
                  <?=$text_environment_sandbox; ?>
                </label>
                <label>
                  <input type="radio" name="indodana_checkout_environment" value="<?=$environment_production; ?>"/>
                  <?=$text_environment_production; ?>
                </label>
              <?php } ?>
              <?php if ($error_environment_empty) { ?>
                <span class="error"><?=$error_environment_empty; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_order_status; ?></td>
            <td><select name="indodana_checkout_default_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $indodana_checkout_default_order_status_id) { ?>
                  <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                <?php } else { ?>
                  <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                <?php } ?>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><?=$entry_status; ?></td>
            <td><select name="indodana_checkout_status">
              <?php if ($indodana_checkout_status) { ?>
                <option value="1" selected="selected"><?=$text_enabled; ?></option>
                <option value="0"><?=$text_disabled; ?></option>
              <?php } else { ?>
                <option value="1"><?=$text_enabled; ?></option>
                <option value="0" selected="selected"><?=$text_disabled; ?></option>
              <?php } ?>
            </select></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?=$footer; ?> 