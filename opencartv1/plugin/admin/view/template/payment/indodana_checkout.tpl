<?=$header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb): ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php endforeach;; ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" /><?=$heading_title; ?></h1>
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
            <td><?=$entry_store_name; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_name" value="<?=$indodana_checkout_store_name; ?>" size="30"/>
              <?php if ($error_store_name) { ?>
                <span class="error"><?=$error_store_name; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_url; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_url" value="<?=$indodana_checkout_store_url; ?>" size="30"/>
              <?php if ($error_store_url) { ?>
                <span class="error"><?=$error_store_url; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_email; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_email" value="<?=$indodana_checkout_store_email; ?>" size="30"/>
              <?php if ($error_store_email) { ?>
                <span class="error"><?=$error_store_email; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_phone; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_phone" value="<?=$indodana_checkout_store_phone; ?>" size="30"/>
              <?php if ($error_store_phone) { ?>
                <span class="error"><?=$error_store_phone; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_country_code; ?></td>
            <td>
              <select name="indodana_checkout_store_country_code">
                <option value="">-- Select --</option>

                <?php foreach ($country_codes as $country_code_id => $country_code_name) { ?>
                <?php if ($country_code_id === $indodana_checkout_store_country_code) { ?>
                <option value="<?php echo $country_code_id; ?>" selected="selected"><?php echo $country_code_name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $country_code_id; ?>"><?php echo $country_code_name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>

              <?php if ($error_store_country_code) { ?>
                <span class="error"><?=$error_store_country_code; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_city; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_city" value="<?=$indodana_checkout_store_city; ?>" size="30"/>
              <?php if ($error_store_city) { ?>
                <span class="error"><?=$error_store_city; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_address; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_address" value="<?=$indodana_checkout_store_address; ?>" size="30"/>
              <?php if ($error_store_address) { ?>
                <span class="error"><?=$error_store_address; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_store_postal_code; ?></td>
            <td>
              <input type="text" name="indodana_checkout_store_postal_code" value="<?=$indodana_checkout_store_postal_code; ?>" size="30"/>
              <?php if ($error_store_postal_code) { ?>
                <span class="error"><?=$error_store_postal_code; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_api_key; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_key" value="<?=$indodana_checkout_api_key; ?>" size="30"/>
              <?php if ($error_api_secret) { ?>
                <span class="error"><?=$error_api_secret; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_api_secret; ?></td>
            <td>
              <input type="text" name="indodana_checkout_api_secret" value="<?=$indodana_checkout_api_secret; ?>" size="30"/>
              <?php if ($error_api_secret) { ?>
                <span class="error"><?=$error_api_secret; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_environment; ?></td>
            <td>
              <?php if ($indodana_checkout_environment === $environment_production) { ?>
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
            <td><?=$entry_default_order_pending_status; ?></td>
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
                  <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_pending_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($error_default_order_pending_status) { ?>
                <span class="error"><?=$error_default_order_pending_status; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_default_order_success_status; ?></td>
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
                  <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_success_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($error_default_order_success_status) { ?>
                <span class="error"><?=$error_default_order_success_status; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_default_order_failed_status; ?></td>
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
                  <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_failed_status_id) { ?>
                    <option value="<?=$order_status['order_status_id']; ?>" selected="selected"><?=$order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?=$order_status['order_status_id']; ?>"><?=$order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>

              <?php if ($error_default_order_failed_status) { ?>
                <span class="error"><?=$error_default_order_failed_status; ?></span>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td><?=$entry_status; ?></td>
            <td><select name="indodana_checkout_status">
              <?php if ($indodana_checkout_status) { ?>
                <option value="1" selected="selected"><?=$text_status_enabled; ?></option>
                <option value="0"><?=$text_status_disabled; ?></option>
              <?php } else { ?>
                <option value="1"><?=$text_status_enabled; ?></option>
                <option value="0" selected="selected"><?=$text_status_disabled; ?></option>
              <?php } ?>
            </select></td>
          </tr>

          <tr>
            <td><?=$entry_sort_order; ?></td>
            <td>
              <input type="text" name="indodana_checkout_sort_order" value="<?=$indodana_checkout_sort_order; ?>" size="30"/>
              <?php if ($error_sort_order) { ?>
                <span class="error"><?=$error_sort_order; ?></span>
              <?php } ?>
            </td>
          </tr>

        </table>
      </form>
    </div>
  </div>
</div>
<?=$footer; ?>
