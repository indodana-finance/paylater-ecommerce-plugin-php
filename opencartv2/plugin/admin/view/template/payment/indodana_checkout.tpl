<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-indodana-config" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><?php echo $button_save; ?></button>
        <a href="<?php echo $form_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><?php echo $button_cancel; ?></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $subheading_title; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data" id="form-indodana" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-name"><?php echo $entry_store_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_name" value="<?php echo $indodana_checkout_store_name; ?>" placeholder="<?php echo $entry_store_name; ?>" id="input-store-name" class="form-control" />
              <?php if ($error_store_name) { ?>
              <div class="text-danger"><?php echo $error_store_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-url"><?php echo $entry_store_url; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_url" value="<?php echo $indodana_checkout_store_url; ?>" placeholder="<?php echo $entry_store_url; ?>" id="input-store-url" class="form-control" />
              <?php if ($error_store_url) { ?>
              <div class="text-danger"><?php echo $error_store_url; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-email"><?php echo $entry_store_email; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_email" value="<?php echo $indodana_checkout_store_email; ?>" placeholder="<?php echo $entry_store_email; ?>" id="input-store-email" class="form-control" />
              <?php if ($error_store_email) { ?>
              <div class="text-danger"><?php echo $error_store_email; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-phone"><?php echo $entry_store_phone; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_phone" value="<?php echo $indodana_checkout_store_phone; ?>" placeholder="<?php echo $entry_store_phone; ?>" id="input-store-phone" class="form-control" />
              <?php if ($error_store_phone) { ?>
              <div class="text-danger"><?php echo $error_store_phone; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-country-code"><?php echo $entry_store_country_code; ?></label>
            <div class="col-sm-10">
              <select name="indodana_checkout_store_country_code" id="input-store-country-code" class="form-control">
                <?php foreach ($country_codes as $country_code_id => $country_code_name) { ?>
                <?php if ($country_code_id === $indodana_checkout_store_country_code) { ?>
                <option value="<?php echo $country_code_id; ?>" selected="selected"><?php echo $country_code_name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $country_code_id; ?>"><?php echo $country_code_name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_store_country_code) { ?>
              <div class="text-danger"><?php echo $error_store_country_code; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-city"><?php echo $entry_store_city; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_city" value="<?php echo $indodana_checkout_store_city; ?>" placeholder="<?php echo $entry_store_city; ?>" id="input-store-city" class="form-control" />
              <?php if ($error_store_city) { ?>
              <div class="text-danger"><?php echo $error_store_address; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-address"><?php echo $entry_store_address; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_address" value="<?php echo $indodana_checkout_store_address; ?>" placeholder="<?php echo $entry_store_address; ?>" id="input-store-address" class="form-control" />
              <?php if ($error_store_address) { ?>
              <div class="text-danger"><?php echo $error_store_address; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store-postal-code"><?php echo $entry_store_postal_code; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_store_postal_code" value="<?php echo $indodana_checkout_store_postal_code; ?>" placeholder="<?php echo $entry_store_postal_code; ?>" id="input-store-postal-code" class="form-control" />
              <?php if ($error_store_postal_code) { ?>
              <div class="text-danger"><?php echo $error_store_postal_code; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-api-key"><?php echo $entry_api_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_api_key" value="<?php echo $indodana_checkout_api_key; ?>" placeholder="<?php echo $entry_api_key; ?>" id="input-api-key" class="form-control" />
              <?php if ($error_api_key) { ?>
              <div class="text-danger"><?php echo $error_api_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-api-secret"><?php echo $entry_api_secret; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_api_secret" value="<?php echo $indodana_checkout_api_secret; ?>" placeholder="<?php echo $entry_api_secret; ?>" id="input-api-secret" class="form-control" />
              <?php if ($error_api_secret) { ?>
              <div class="text-danger"><?php echo $error_api_secret; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-environment"><?php echo $entry_environment; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($indodana_checkout_environment !== $environment_production) { ?>
                <input type="radio" name="indodana_checkout_environment" value="<?=$environment_sandbox; ?>" checked/>
                <?=$text_environment_sandbox; ?>
                <?php } else { ?>
                <input type="radio" name="indodana_checkout_environment" value="<?=$environment_sandbox; ?>"/>
                <?=$text_environment_sandbox; ?>
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($indodana_checkout_environment === $environment_production) { ?>
                <input type="radio" name="indodana_checkout_environment" value="<?=$environment_production; ?>" checked/>
                <?=$text_environment_production; ?>
                <?php } else { ?>
                <input type="radio" name="indodana_checkout_environment" value="<?=$environment_production; ?>"/>
                <?=$text_environment_production; ?>
                <?php } ?>
              </label>
              <?php if ($error_environment) { ?>
              <div class="text-danger"><?php echo $error_environment; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-pending-status"><?php echo $entry_default_order_pending_status; ?></label>
            <div class="col-sm-10">
              <select name="indodana_checkout_default_order_pending_status_id" id="input-order-pending-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_default_order_pending_status) { ?>
              <div class="text-danger"><?php echo $error_default_order_pending_status; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-success-status"><?php echo $entry_default_order_success_status; ?></label>
            <div class="col-sm-10">
              <select name="indodana_checkout_default_order_success_status_id" id="input-order-success-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_success_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_default_order_success_status) { ?>
              <div class="text-danger"><?php echo $error_default_order_success_status; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-failed-status"><?php echo $entry_default_order_failed_status; ?></label>
            <div class="col-sm-10">
              <select name="indodana_checkout_default_order_failed_status_id" id="input-order-failed-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] === $indodana_checkout_default_order_failed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_default_order_failed_status) { ?>
              <div class="text-danger"><?php echo $error_default_order_failed_status; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="indodana_checkout_status" id="input-status" class="form-control">
                <?php if ($indodana_checkout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_status_enabled; ?></option>
                <option value="0"><?php echo $text_status_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_status_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_status_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="indodana_checkout_sort_order" value="<?php echo $indodana_checkout_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
              <?php if ($error_sort_order) { ?>
              <div class="text-danger"><?php echo $error_sort_order; ?></div>
              <?php } ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
