<?php
require_once DIR_SYSTEM . 'library/indodana/autoload.php';

use Respect\Validation\Validator;

class ControllerPaymentIndodanaCheckout extends Controller {
  private $errors = array();

  private $fieldsToBePrefilledBeforeShowingForm = [
    'indodana_checkout_api_key',
    'indodana_checkout_api_secret',
    'indodana_checkout_default_order_failed_status_id',
    'indodana_checkout_default_order_pending_status_id',
    'indodana_checkout_default_order_success_status_id',
    'indodana_checkout_environment',
    'indodana_checkout_sort_order',
    'indodana_checkout_sort_order',
    'indodana_checkout_status',
    'indodana_store_address',
    'indodana_store_city',
    'indodana_store_country_code',
    'indodana_store_email',
    'indodana_store_name',
    'indodana_store_phone',
    'indodana_store_postal_code',
    'indodana_store_url',
  ];

  private $fieldValidatorMapping;

  public function index()
  {
    $this->loadModel();
    $this->setupValidationRules();

    /*
        In OPENCART, every successful submit through the form will be redirected to the same page
        with the value from the previous form sent as POST data.

        We need to check if
        1. the received request contains the previous form's data (apply the data and redirect to home page)
        2. doesn't contain the previous form's data (give user the form)
     */
    if ($this->receiveConfigurationData() && $this->validate()) {
      $this->applyConfiguration();
      $this->redirectToExtensionPage();
    }

    $this->initializeLanguageData();
    $this->initializeFormAction();
    $this->initializeErrors();
    $this->initializeLog();

    $this->loadErrors();
    $this->applyDefaultValue();
    $this->initializeView();

    $this->response->setOutput($this->render());
  }

  private function setupValidationRules() {
    // Well surely there are better ways to do validation for this,
    // but let's just do this for now to reduce chance to break other things.
    $this->fieldValidatorMapping = [
      'indodana_store_name'          => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_store_name_validation_error',
      ],
      'indodana_store_url'           => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_store_url_validation_error',
      ],
      'indodana_store_email'         => [
        'validator' => Validator::notOptional()->email(),
        'errorKey' => 'indodana_store_email_validation_error',
      ],
      'indodana_store_phone'         => [
        'validator' => Validator::notOptional()->numeric(),
        'errorKey' => 'indodana_store_phone_validation_error',
      ],
      'indodana_store_country_code'  => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_store_country_code_validation_error',
      ],
      'indodana_store_city'          => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_store_city_validation_error',
      ],
      'indodana_store_address'       => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_store_address_validation_error',
      ],
      'indodana_store_postal_code'   => [
        'validator' => Validator::notOptional()->length(null, 5),
        'errorKey' => 'indodana_store_postal_code_validation_error',
      ],
      'indodana_checkout_api_secret' => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_api_secret_validation_error',
      ],
      'indodana_checkout_api_key'    => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_api_key_validation_error',
      ],
      'indodana_checkout_sort_order' => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_sort_order_validation_error',
      ],
      'indodana_checkout_default_order_failed_status_id' => [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_default_order_failed_status_id_validation_error',
      ],
      'indodana_checkout_default_order_pending_status_id'=> [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_default_order_pending_status_id_validation_error',
      ],
      'indodana_checkout_default_order_success_status_id'=> [
        'validator' => Validator::notOptional(),
        'errorKey' => 'indodana_checkout_default_order_success_status_id_validation_error',
      ],
    ];
  }

  private function loadModel() {
    $this->language->load('payment/indodana_checkout');
    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');
    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
  }

  private function redirectToExtensionPage()
  {
    $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token']));
  }

  private function applyConfiguration()
  {
    $this->model_setting_setting->editSetting('indodana_checkout', $this->request->post);
    $this->session->data['success'] = $this->language->get('text_success');
  }

  private function receiveConfigurationData() {
    return ($this->request->server['REQUEST_METHOD'] == 'POST');
  }

  private function initializeView() {
    $this->template = 'payment/indodana_checkout.tpl';
    $this->children = array(
      'common/header',
      'common/footer'
    );
  }

  private function initializeLog() {
    $this->data['log'] = IndodanaLogger::read();
  }

    /*
        When user press EDIT or ADD, we need to show the previous data to the user
        This function will get all the form's data that might have been saved before, and show it
     */
  private function applyDefaultValue() {
    foreach ($this->fieldsToBePrefilledBeforeShowingForm as $key) {
      if (isset($this->request->post[$key])) {
        $this->data[$key] = $this->request->post[$key];
      } else {
        $this->data[$key] = $this->config->get($key);
      }
    }
  }

  private function initializeLanguageData() {
    $this->document->setTitle($this->language->get('heading_title'));
    $languageKeys = [
      'heading_title',
      'entry_status',
      'text_enabled',
      'text_disabled',
      'text_success',
      'text_button_save',
      'text_button_cancel',
      'text_environment_sandbox',
      'text_environment_production',
      'entry_order_success_status',
      'entry_order_failed_status',
      'entry_order_pending_status',
      'entry_api_secret',
      'entry_api_key',
      'entry_environment',
      'entry_sort_order',
      'country_codes',
      'environment_sandbox',
      'environment_production'
    ];

    foreach($languageKeys as $key) {
      $this->data[$key] = $this->language->get($key);
    }
  }

  private function initializeFormAction() {
    $this->data['form_action'] = $this->url->link('payment/indodana_checkout', 'token=' . $this->session->data['token']);
    $this->data['form_cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token']);
  }

  private function initializeErrors() {
    $this->data['errors'] = $this->errors;
  }

  private function loadErrors() {
    $errorKeys = array_map(function ($validatorConfig) {
      return $validatorConfig['errorKey'];
    }, array_values($this->fieldValidatorMapping));

    foreach($errorKeys as $key) {
      if (isset($this->errors[$key])) {
        $this->data[$key] = $this->errors[$key];
      } else {
        $this->data[$key] = '';
      }
    }
  }

  private function validate() {
    if (!$this->user->hasPermission('modify', 'payment/indodana_checkout')) {
      $this->errors['error_permission'] = $this->language->get('error_permission');
    }

    foreach($this->fieldValidatorMapping as $fieldName => $validatorConfig) {
      $validator = $validatorConfig['validator'];
      $errorKey = $validatorConfig['errorKey'];

      if (!$validator->validate($this->request->post[$fieldName])) {
        $this->errors[$errorKey] = $this->language->get($errorKey);
      }
    };

    return empty($this->errors);
  }
}
