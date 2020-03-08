<?php

require_once DIR_SYSTEM . 'library/indodana/autoload.php';

use IndodanaCommon\IndodanaService;
use IndodanaCommon\IndodanaConstant;

class ControllerPaymentIndodanaCheckout extends Controller {
  private $errors = [];
  private $data = [];
  private $labelKeyName = 'label';
  private $inputKeyName = 'input';
  private $errorKeyName = 'error';
  private $indodanaCheckoutMapping = [];
  private $indodanaCheckoutConfigKeys = [];
  private $environmentMapping = [];
  private $statusMapping = [];

  /* 
      In OPENCART, every successful submit through the form will be redirected to the same page
      with the value from the previous form sent as POST data.

      We need to check if 
      1. the received request contains the previous form's data (apply the data and redirect to home page)
      2. doesn't contain the previous form's data (give user the form)
   */
  public function index() 
  {
    $this->init();
    $this->loadModel();
    $this->applyFormValue();

    // When merchant hit "Save" button
    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
      $this->applyConfiguration();
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
    $labelKey = $this->labelKeyName;
    $inputKey = $this->inputKeyName;
    $errorKey = $this->errorKeyName;

    $this->indodanaCheckoutMapping = [
      'storeName' => [
        $labelKey => 'entry_store_name',
        $inputKey => 'indodana_checkout_store_name',
        $errorKey => 'error_store_name',
      ],
      'storeUrl' => [
        $labelKey => 'entry_store_url',
        $inputKey => 'indodana_checkout_store_url',
        $errorKey => 'error_store_url',
      ],
      'storeEmail' => [
        $labelKey => 'entry_store_email',
        $inputKey => 'indodana_checkout_store_email',
        $errorKey => 'error_store_email',
      ],
      'storePhone' => [
        $labelKey => 'entry_store_phone',
        $inputKey => 'indodana_checkout_store_phone',
        $errorKey => 'error_store_phone',
      ],
      'storeCountryCode' => [
        $labelKey => 'entry_store_country_code',
        $inputKey => 'indodana_checkout_store_country_code',
        $errorKey => 'error_store_country_code',
      ],
      'storeCity' => [
        $labelKey => 'entry_store_city',
        $inputKey => 'indodana_checkout_store_city',
        $errorKey => 'error_store_city',
      ],
      'storeAddress' => [
        $labelKey => 'entry_store_address',
        $inputKey => 'indodana_checkout_store_address',
        $errorKey => 'error_store_address',
      ],
      'storePostalCode' => [
        $labelKey => 'entry_store_postal_code',
        $inputKey => 'indodana_checkout_store_postal_code',
        $errorKey => 'error_store_postal_code',
      ],
      'apiKey' => [
        $labelKey => 'entry_api_key',
        $inputKey => 'indodana_checkout_api_key',
        $errorKey => 'error_api_key',
      ],
      'apiSecret' => [
        $labelKey => 'entry_api_secret',
        $inputKey => 'indodana_checkout_api_secret',
        $errorKey => 'error_api_secret',
      ],
      'environment' => [
        $labelKey => 'entry_environment',
        $inputKey => 'indodana_checkout_environment',
        $errorKey => 'error_environment',
      ],
      'defaultOrderPendingStatus' => [
        $labelKey => 'entry_default_order_pending_status',
        $inputKey => 'indodana_checkout_default_order_pending_status_id',
        $errorKey => 'error_default_order_pending_status',
      ],
      'defaultOrderSuccessStatus' => [
        $labelKey => 'entry_default_order_success_status',
        $inputKey => 'indodana_checkout_default_order_success_status_id',
        $errorKey => 'error_default_order_success_status',
      ],
      'defaultOrderFailedStatus' => [
        $labelKey => 'entry_default_order_failed_status',
        $inputKey => 'indodana_checkout_default_order_failed_status_id',
        $errorKey => 'error_default_order_failed_status',
      ],
      'status' => [
        $labelKey => 'entry_status',
        $inputKey => 'indodana_checkout_status',
        $errorKey => 'error_status',
      ],
      'sortOrder' => [
        $labelKey => 'entry_sort_order',
        $inputKey => 'indodana_checkout_sort_order',
        $errorKey => 'error_sort_order',
      ],
    ];

    $this->indodanaCheckoutConfigKeys = array_keys($this->indodanaCheckoutMapping);

    $this->environmentMapping = IndodanaConstant::getEnvironmentMapping();

    $this->statusMapping = IndodanaConstant::getStatusMapping();
  }

  private function loadModel() 
  {
		$this->load->language('payment/indodana_checkout');

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');

    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    $this->data['country_codes'] = IndodanaConstant::getCountryCodeMapping();
  }

  /*
      When user press EDIT or ADD, we need to show the previous data to the user
      This function will get all the form's data that might have been saved before, and show it
   */
  private function applyFormValue() 
  {
    foreach ($this->indodanaCheckoutConfigKeys as $configKey) {
      $inputKey = $this->indodanaCheckoutMapping[$configKey][$this->inputKeyName];

      if (isset($this->request->post[$inputKey])) {
        $this->data[$inputKey] = $this->request->post[$inputKey];
      } else {
        $this->data[$inputKey] = $this->config->get($inputKey);
      }
    }
  }

  private function applyConfiguration() 
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
    $languageKeys = [
      'heading_title',
      'subheading_title',
      'button_save',
      'button_cancel',
      'text_success',
    ];

    foreach($languageKeys as $key) {
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
    $frontendConfigMapping = IndodanaConstant::getFrontendConfigMapping();

    foreach ($this->indodanaCheckoutConfigKeys as $configKey) {
      $labelKey = $this->indodanaCheckoutMapping[$configKey][$this->labelKeyName];

      $this->data[$labelKey] = $frontendConfigMapping[$configKey];
    }

    // Environment Value
    $this->data['environment_sandbox'] = IndodanaConstant::SANDBOX;
    $this->data['environment_production'] = IndodanaConstant::PRODUCTION;

    // Environment Text
    $this->data['text_environment_sandbox'] = $this->environmentMapping[IndodanaConstant::SANDBOX];
    $this->data['text_environment_production'] = $this->environmentMapping[IndodanaConstant::PRODUCTION];

    // Status Value
    $this->data['status_disabled'] = IndodanaConstant::DISABLED;
    $this->data['status_enabled'] = IndodanaConstant::ENABLED;

    // Status Text
    $this->data['text_status_disabled'] = $this->statusMapping[IndodanaConstant::DISABLED];
    $this->data['text_status_enabled'] = $this->statusMapping[IndodanaConstant::ENABLED];

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
    foreach ($this->indodanaCheckoutConfigKeys as $configKey) {
      $errorKey = $this->indodanaCheckoutMapping[$configKey][$this->errorKeyName];

      if (isset($this->errors[$errorKey])) {
        $this->data[$errorKey] = $this->errors[$errorKey];
      } else {
        $this->data[$errorKey] = '';
      }
    }
  }

  private function validate() 
  {
    if (!$this->user->hasPermission('modify', 'payment/indodana_checkout')) {
      $this->errors['error_permission'] = $this->language->get('error_permission');
    }

    $configuration = [];

    foreach ($this->indodanaCheckoutConfigKeys as $configKey) {
      $inputKey = $this->indodanaCheckoutMapping[$configKey][$this->inputKeyName];

      $inputValue = isset($this->data[$inputKey]) ? $this->data[$inputKey] : null;

      $configuration[$configKey] = $inputValue;

      if ($configKey === 'sortOrder') {
        $configuration[$configKey] = (int) $configuration[$configKey];
      }
    }

    $validationResult = IndodanaService::validateConfiguration($configuration);

    foreach ($validationResult['errors'] as $validationErrorKey => $validationErrorValue) {
      $errorKey = $this->indodanaCheckoutMapping[$validationErrorKey][$this->errorKeyName];

      $this->errors[$errorKey] = $validationErrorValue;
    }

    return empty($this->errors);
  }
}
