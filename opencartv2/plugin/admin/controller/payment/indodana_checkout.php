<?php

require_once DIR_SYSTEM . 'library/indodana/autoload.php';

use IndodanaCommon\IndodanaService;
use IndodanaCommon\IndodanaConstant;

class ControllerPaymentIndodanaCheckout extends Controller {
  private $data = [];
  private $errors = [];
  private $label_key_name = 'label';
  private $input_key_name = 'input';
  private $error_key_name = 'error';
  private $indodana_checkout_mapping = [];
  private $indodana_checkout_config_keys = [];
  private $environment_mapping = [];
  private $status_mapping = [];

  public function index() 
  {
    $this->init();
    $this->loadModel();
    $this->applyFormValue();

    // When merchant hit "Save" button
    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
      $this->saveConfiguration();
      $this->redirectToExtensionPage();
    }

    $this->initializeLanguage();
    $this->initializeForm();
    $this->initializeErrors();

    $this->loadErrors();
    $this->initializeView();

		$this->response->setOutput($this->load->view('payment/indodana_checkout.tpl', $this->data));
  }

  private function init()
  {
    $label_key = $this->label_key_name;
    $input_key = $this->input_key_name;
    $error_key = $this->error_key_name;

    $this->indodana_checkout_mapping = [
      'storeName' => [
        $label_key => 'entry_store_name',
        $input_key => 'indodana_checkout_store_name',
        $error_key => 'error_store_name',
      ],
      'storeUrl' => [
        $label_key => 'entry_store_url',
        $input_key => 'indodana_checkout_store_url',
        $error_key => 'error_store_url',
      ],
      'storeEmail' => [
        $label_key => 'entry_store_email',
        $input_key => 'indodana_checkout_store_email',
        $error_key => 'error_store_email',
      ],
      'storePhone' => [
        $label_key => 'entry_store_phone',
        $input_key => 'indodana_checkout_store_phone',
        $error_key => 'error_store_phone',
      ],
      'storeCountryCode' => [
        $label_key => 'entry_store_country_code',
        $input_key => 'indodana_checkout_store_country_code',
        $error_key => 'error_store_country_code',
      ],
      'storeCity' => [
        $label_key => 'entry_store_city',
        $input_key => 'indodana_checkout_store_city',
        $error_key => 'error_store_city',
      ],
      'storeAddress' => [
        $label_key => 'entry_store_address',
        $input_key => 'indodana_checkout_store_address',
        $error_key => 'error_store_address',
      ],
      'storePostalCode' => [
        $label_key => 'entry_store_postal_code',
        $input_key => 'indodana_checkout_store_postal_code',
        $error_key => 'error_store_postal_code',
      ],
      'apiKey' => [
        $label_key => 'entry_api_key',
        $input_key => 'indodana_checkout_api_key',
        $error_key => 'error_api_key',
      ],
      'apiSecret' => [
        $label_key => 'entry_api_secret',
        $input_key => 'indodana_checkout_api_secret',
        $error_key => 'error_api_secret',
      ],
      'environment' => [
        $label_key => 'entry_environment',
        $input_key => 'indodana_checkout_environment',
        $error_key => 'error_environment',
      ],
      'defaultOrderPendingStatus' => [
        $label_key => 'entry_default_order_pending_status',
        $input_key => 'indodana_checkout_default_order_pending_status_id',
        $error_key => 'error_default_order_pending_status',
      ],
      'defaultOrderSuccessStatus' => [
        $label_key => 'entry_default_order_success_status',
        $input_key => 'indodana_checkout_default_order_success_status_id',
        $error_key => 'error_default_order_success_status',
      ],
      'defaultOrderFailedStatus' => [
        $label_key => 'entry_default_order_failed_status',
        $input_key => 'indodana_checkout_default_order_failed_status_id',
        $error_key => 'error_default_order_failed_status',
      ],
      'status' => [
        $label_key => 'entry_status',
        $input_key => 'indodana_checkout_status',
        $error_key => 'error_status',
      ],
      'sortOrder' => [
        $label_key => 'entry_sort_order',
        $input_key => 'indodana_checkout_sort_order',
        $error_key => 'error_sort_order',
      ],
    ];

    $this->indodana_checkout_config_keys = array_keys($this->indodana_checkout_mapping);

    $this->environment_mapping = IndodanaConstant::getEnvironmentMapping();

    $this->status_mapping = IndodanaConstant::getStatusMapping();
  }

  private function loadModel() 
  {
		$this->load->language('payment/indodana_checkout');

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');

    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    $this->data['country_codes'] = IndodanaConstant::getCountryCodeMapping();
  }

  /**
   * Display form value from saved configuration if exist
   */
  private function applyFormValue() 
  {
    foreach ($this->indodana_checkout_config_keys as $config_key) {
      $input_key = $this->indodana_checkout_mapping[$config_key][$this->input_key_name];

      if (isset($this->request->post[$input_key])) {
        $this->data[$input_key] = $this->request->post[$input_key];
      } else {
        $this->data[$input_key] = $this->config->get($input_key);
      }
    }
  }

  private function saveConfiguration() 
  {
    $this->model_setting_setting->editSetting('indodana_checkout', $this->request->post);

    $this->session->data['success'] = $this->language->get('text_success');
  }

  private function redirectToExtensionPage() 
  {
    $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token']));
  }

  private function initializeLanguage() 
  {
    $language_keys = [
      'heading_title',
      'subheading_title',
      'button_save',
      'button_cancel',
      'text_success',
    ];

    foreach($language_keys as $key) {
      $this->data[$key] = $this->language->get($key);
    }

    $this->document->setTitle($this->data['heading_title']);
  }

  private function initializeView() 
  {
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
  }

  private function initializeForm()
  {
    // Label and placeholder
    $frontend_config_mapping = IndodanaConstant::getFrontendConfigMapping();

    foreach ($this->indodana_checkout_config_keys as $config_key) {
      $label_key = $this->indodana_checkout_mapping[$config_key][$this->label_key_name];

      $this->data[$label_key] = $frontend_config_mapping[$config_key];
    }

    // Environment Value
    $this->data['environment_sandbox'] = IndodanaConstant::SANDBOX;
    $this->data['environment_production'] = IndodanaConstant::PRODUCTION;

    // Environment Text
    $this->data['text_environment_sandbox'] = $this->environment_mapping[IndodanaConstant::SANDBOX];
    $this->data['text_environment_production'] = $this->environment_mapping[IndodanaConstant::PRODUCTION];

    // Status Value
    $this->data['status_disabled'] = IndodanaConstant::DISABLED;
    $this->data['status_enabled'] = IndodanaConstant::ENABLED;

    // Status Text
    $this->data['text_status_disabled'] = $this->status_mapping[IndodanaConstant::DISABLED];
    $this->data['text_status_enabled'] = $this->status_mapping[IndodanaConstant::ENABLED];

    // Action
    $this->data['form_action'] = $this->url->link('payment/indodana_checkout', 'token=' . $this->session->data['token']);
    $this->data['form_cancel'] = $this->url->link('payment', 'token=' . $this->session->data['token']);
  }

  private function initializeErrors() 
  {
    $this->data['errors'] = $this->errors;
  }

  private function loadErrors() 
  {
    foreach ($this->indodana_checkout_config_keys as $config_key) {
      $error_key = $this->indodana_checkout_mapping[$config_key][$this->error_key_name];

      if (isset($this->errors[$error_key])) {
        $this->data[$error_key] = $this->errors[$error_key];
      } else {
        $this->data[$error_key] = '';
      }
    }
  }

  private function validate() 
  {
    if (!$this->user->hasPermission('modify', 'payment/indodana_checkout')) {
      $this->errors['error_permission'] = $this->language->get('error_permission');
    }

    $configuration = [];

    foreach ($this->indodana_checkout_config_keys as $config_key) {
      $input_key = $this->indodana_checkout_mapping[$config_key][$this->input_key_name];

      $input_value = isset($this->data[$input_key]) ? $this->data[$input_key] : null;

      $configuration[$config_key] = $input_value;

      if ($config_key === 'sortOrder') {
        $configuration[$config_key] = (int) $configuration[$config_key];
      }
    }

    $validation_result = IndodanaService::validateConfiguration($configuration);

    foreach ($validation_result['errors'] as $validation_error_key => $validation_error_value) {
      $error_key = $this->indodana_checkout_mapping[$validation_error_key][$this->error_key_name];

      $this->errors[$error_key] = $validation_error_value;
    }

    return empty($this->errors);
  }
}
