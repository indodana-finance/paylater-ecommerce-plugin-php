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
            <td><h2>Indodana Paylater Configuration</h2></td>
          </tr>

          <tr>
            <td>Store Name</td>
            <td>
              <input type="text" name="indodana_store_name" value="<?=$indodana_store_name; ?>" size="30"/>
              <?php if ($indodana_store_name_validation_error) { ?>
                <span class="error"><?=$indodana_store_name_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Url</td>
            <td>
              <input type="text" name="indodana_store_url" value="<?=$indodana_store_url; ?>" size="30"/>
              <?php if ($indodana_store_url_validation_error) { ?>
                <span class="error"><?=$indodana_store_name_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Email</td>
            <td>
              <input type="text" name="indodana_store_email" value="<?=$indodana_store_email; ?>" size="30"/>
              <?php if ($indodana_store_email_validation_error) { ?>
                <span class="error"><?=$indodana_store_email_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Phone</td>
            <td>
              <input type="text" name="indodana_store_phone" value="<?=$indodana_store_phone; ?>" size="30"/>
              <?php if ($indodana_store_phone_validation_error) { ?>
                <span class="error"><?=$indodana_store_phone_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Country Code</td>
            <td>
              <select name="indodana_store_country_code">
                <option value="">-- Select --</option>
                <option
                  value="ID"
                  <?php if ($indodana_store_country_code === "ID") { ?>
                  selected="selected"
                  <?php } ?>
                >ID</option>
              </select>

              <?php if ($indodana_store_country_code_validation_error) { ?>
                <span class="error"><?=$indodana_store_country_code_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store City</td>
            <td>
              <input type="text" name="indodana_store_city" value="<?=$indodana_store_city; ?>" size="30"/>
              <?php if ($indodana_store_city_validation_error) { ?>
                <span class="error"><?=$indodana_store_city_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Address</td>
            <td>
              <input type="text" name="indodana_store_address" value="<?=$indodana_store_address; ?>" size="30"/>
              <?php if ($indodana_store_address_validation_error) { ?>
                <span class="error"><?=$indodana_store_address_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Store Postal Code</td>
            <td>
              <input type="text" name="indodana_store_postal_code" value="<?=$indodana_store_postal_code; ?>" size="30"/>
              <?php if ($indodana_store_postal_code_validation_error) { ?>
                <span class="error"><?=$indodana_store_postal_code_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_api_secret; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_secret" value="<?=$indodana_checkout_api_secret; ?>" size="30"/>
              <?php if ($indodana_checkout_api_secret_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_api_secret_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_api_key; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_key" value="<?=$indodana_checkout_api_key; ?>" size="30"/>
              <?php if ($indodana_checkout_api_key_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_api_key_validation_error; ?></span>
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
            </td>
          </tr>
          <tr>
            <td><?=$entry_order_pending_status; ?></td>
            <td>
              <select name="indodana_checkout_default_order_pending_status_id">
                <option
                  value=""
                  <?php if (!$indodana_checkout_default_order_pending_status_id) { ?>
                  selected="selected"
                  <?php } ?>
                >
                  -- Select --
                </option>

                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $indodana_checkout_default_order_pending_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($indodana_checkout_default_order_pending_status_id_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_default_order_pending_status_id_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_order_success_status; ?></td>
            <td>
              <select name="indodana_checkout_default_order_success_status_id">
                <option
                  value=""
                  <?php if (!$indodana_checkout_default_order_success_status_id) { ?>
                  selected="selected"
                  <?php } ?>
                >
                  -- Select --
                </option>

                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $indodana_checkout_default_order_success_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($indodana_checkout_default_order_success_status_id_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_default_order_success_status_id_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?=$entry_order_failed_status; ?></td>
            <td>
              <select name="indodana_checkout_default_order_failed_status_id">
                <option
                  value=""
                  <?php if (!$indodana_checkout_default_order_failed_status_id) { ?>
                  selected="selected"
                  <?php } ?>
                >
                  -- Select --
                </option>

                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $indodana_checkout_default_order_failed_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($indodana_checkout_default_order_failed_status_id_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_default_order_failed_status_id_validation_error; ?></span>
              <?php } ?>
            </td>
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
          <tr>
            <td><?=$entry_sort_order; ?></td>
            <td>
              <input type="text" name="indodana_checkout_sort_order" value="<?=$indodana_checkout_sort_order; ?>" size="30"/>
              <?php if ($indodana_checkout_sort_order_validation_error) { ?>
                <span class="error"><?=$indodana_checkout_sort_order_validation_error; ?></span>
              <?php } ?>
            </td>
          </tr>
        </table>
      </form>
      <h2>Log</h2>
      <textarea
        wrap="off"
        style="width: 98%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"
      ><?= $log; ?></textarea>
    </div>
  </div>
</div>
<?=$footer; ?>
