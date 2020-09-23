<?php

// ignore vscode's phpcs extension missing namespace error
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

require_once(_PS_MODULE_DIR_ . 'indodana' . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'IndodanaTools.php');

class Indodana extends PaymentModule
{
  public $moduleConfigs = [];

  public function __construct()
  {
    $this->name = 'indodana';
    $this->tab = 'payments_gateways';
    $this->version = '0.1.0';
    $this->author = 'Indodana';
    $this->need_instance = 0;

    /**
     * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
     */
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = Configuration::get('INDODANA_TITLE') != '' ? Configuration::get('INDODANA_TITLE') : $this->l('Indodana PayLater');
    $this->description = $this->l('Indodana PayLater redirects customers to Indodana during checkout.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    $this->limited_countries = ['ID'];
    $this->limited_currencies = ['IDR'];
    $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

    $this->moduleConfigs = [
      'INDODANA_ENABLE_TRUE' => 1,
      'INDODANA_TITLE' => 'Indodana PayLater',
      'INDODANA_DESCRIPTION' => 'Pay with installment via our PayLater product.',
      'INDODANA_ENVIRONMENT' => 'SANDBOX',
      'INDODANA_API_KEY' => '',
      'INDODANA_API_SECRET' => '',
      'INDODANA_API_KEY_PRODUCTION' => '',
      'INDODANA_API_SECRET_PRODUCTION' => '',
      'INDODANA_STORE_NAME' => '',
      'INDODANA_STORE_URL' => '',
      'INDODANA_STORE_EMAIL' => '',
      'INDODANA_STORE_PHONE' => '',
      'INDODANA_STORE_COUNTRY_CODE' => 'IDN',
      'INDODANA_STORE_CITY' => '',
      'INDODANA_STORE_ADDRESS' => '',
      'INDODANA_STORE_POSTAL_CODE' => '',
      'INDODANA_DEFAULT_ORDER_PENDING_STATUS' => '',
      'INDODANA_DEFAULT_ORDER_SUCCESS_STATUS' => '2', // default: payment accepted
      'INDODANA_DEFAULT_ORDER_FAILED_STATUS' => '6',  // default: cancelled
    ];
  }

  /**
   * Don't forget to create update methods if needed:
   * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
   */
  public function install()
  {
    if (extension_loaded('curl') == false) {
      $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
      return false;
    }

    $isoCode = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
    if (in_array($isoCode, $this->limited_countries) == false) {
      $this->_errors[] = $this->l('This module is not available in your country');
      return false;
    }

    // Registration order status
    if (!$this->installOrderState()) {
      return false;
    }

    $shops = Shop::getShops();
    $this->moduleConfigs['INDODANA_DEFAULT_ORDER_PENDING_STATUS'] = (int) Configuration::get('INDODANA_OS_WAITING');
    foreach ($this->moduleConfigs as $key => $value) {
      if (Shop::isFeatureActive()) {
        foreach ($shops as $shop) {
          if (!Configuration::updateValue($key, $value, false, null, (int) $shop['id_shop'])) {
            return false;
          }
        }
      } else {
        if (!Configuration::updateValue($key, $value)) {
          return false;
        }
      }
    }

    if (
      !parent::install()
      || !$this->registerHook('payment')
      || !$this->registerHook('paymentReturn')
      || !$this->registerHook('paymentOptions')
      || !$this->registerHook('displayPayment')
      || !$this->registerHook('displayPaymentReturn')
    ) {
      return false;
    }

    return true;
  }

  /**
   * Create order state
   * @return boolean
   */
  public function installOrderState()
  {
    if (
      !Configuration::get('INDODANA_OS_WAITING')
      || !Validate::isLoadedObject(new OrderState(Configuration::get('INDODANA_OS_WAITING')))
    ) {
      $orderState = new OrderState();
      $orderState->name = [];
      foreach (Language::getLanguages() as $language) {
        if (Tools::strtolower($language['iso_code']) == 'id') {
          $orderState->name[$language['id_lang']] = 'Menunggu pembayaran Indodana';
        } else {
          $orderState->name[$language['id_lang']] = 'Awaiting for Indodana payment';
        }
      }
      $orderState->send_email = false;
      $orderState->color = '#FFBA00';
      $orderState->hidden = false;
      $orderState->delivery = false;
      $orderState->logable = false;
      $orderState->invoice = false;
      $orderState->module_name = $this->name;
      if ($orderState->add()) {
        $source = _PS_MODULE_DIR_ . 'indodana/logo.png';
        $destination = _PS_ROOT_DIR_ . '/img/os/' . (int) $orderState->id . '.gif';
        copy($source, $destination);
      }

      if (Shop::isFeatureActive()) {
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
          Configuration::updateValue('INDODANA_OS_WAITING', (int) $orderState->id, false, null, (int) $shop['id_shop']);
        }
      } else {
        Configuration::updateValue('INDODANA_OS_WAITING', (int) $orderState->id);
      }
    }

    return true;
  }

  public function uninstall()
  {
    foreach ($this->moduleConfigs as $key => $value) {
      if (!Configuration::deleteByName($key)) {
        return false;
      }
    }

    return parent::uninstall();
  }

  /**
   * Load the configuration form
   */
  public function getContent()
  {
    $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

    /**
     * If values have been submitted in the form, process.
     */
    if (((bool) Tools::isSubmit('submitIndodanaModule')) == true) {
      $env = Tools::getValue('INDODANA_ENVIRONMENT');
      $isValid = [];

      if (!Validate::isUrl(Tools::getValue('INDODANA_STORE_URL'))) {
        $output .= $this->displayError($this->l('Invalid store url value'));
        $isValid[] = false;
      }

      if (!Validate::isEmail(Tools::getValue('INDODANA_STORE_EMAIL'))) {
        $output .= $this->displayError($this->l('Invalid store email value'));
        $isValid[] = false;
      }

      if (!Validate::isPhoneNumber(Tools::getValue('INDODANA_STORE_PHONE'))) {
        $output .= $this->displayError($this->l('Invalid store phone value'));
        $isValid[] = false;
      }

      if (!Validate::isInt(Tools::getValue('INDODANA_STORE_POSTAL_CODE'))) {
        $output .= $this->displayError($this->l('Invalid store postal code value'));
        $isValid[] = false;
      }

      if (empty(Tools::getValue('INDODANA_API_KEY')) && $env == 'SANDBOX') {
        $output .= $this->displayError($this->l('Sandbox API Key is required'));
        $isValid[] = false;
      }

      if (empty(Tools::getValue('INDODANA_API_SECRET')) && $env == 'SANDBOX') {
        $output .= $this->displayError($this->l('Sandbox API Secret is required'));
        $isValid[] = false;
      }

      if (empty(Tools::getValue('INDODANA_API_KEY_PRODUCTION')) && $env == 'PRODUCTION') {
        $output .= $this->displayError($this->l('Production API Key is required'));
        $isValid[] = false;
      }

      if (empty(Tools::getValue('INDODANA_API_SECRET_PRODUCTION')) && $env == 'PRODUCTION') {
        $output .= $this->displayError($this->l('Production API Secret is required'));
        $isValid[] = false;
      }

      $isValid[] = $this->checkRequiredConfigValue('INDODANA_TITLE', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_DESCRIPTION', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_STORE_NAME', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_STORE_CITY', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_STORE_ADDRESS', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_DEFAULT_ORDER_PENDING_STATUS', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_DEFAULT_ORDER_SUCCESS_STATUS', $output);
      $isValid[] = $this->checkRequiredConfigValue('INDODANA_DEFAULT_ORDER_FAILED_STATUS', $output);

      $this->postProcess();

      if (!in_array(false, $isValid)) {
        $output .= $this->displayConfirmation($this->l('Settings updated'));
      }
    }

    return $output . $this->renderForm();
  }

  private function checkRequiredConfigValue($configName, &$output)
  {
    $isValid = true;
    if ((empty(Tools::getValue($configName)))) {
      // convert config name to more readable text
      $message = ucwords(strtolower(str_replace('_', ' ', substr($configName, 9))));
      $output .= $this->displayError($this->l($message . ' is required'));
      $isValid = false;
    }

    return $isValid;
  }

  /**
   * Create the form that will be displayed in the configuration of your module.
   */
  protected function renderForm()
  {
    $helper = new HelperForm();

    $helper->show_toolbar = false;
    $helper->table = $this->table;
    $helper->module = $this;
    $helper->default_form_language = $this->context->language->id;
    $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

    $helper->identifier = $this->identifier;
    $helper->submit_action = 'submitIndodanaModule';
    $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
      . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');

    $helper->tpl_vars = [
      'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
      'languages' => $this->context->controller->getLanguages(),
      'id_language' => $this->context->language->id,
    ];

    return $helper->generateForm([$this->getConfigForm()]);
  }

  /**
   * Create the structure of your form.
   */
  protected function getConfigForm()
  {
    $orderStates = OrderState::getOrderStates(1);
    $options = [];
    foreach ($orderStates as $state) {
      $options[] = [
        'id_option' => $state['id_order_state'],
        'name' => $state['name']
      ];
    }

    return [
      'form' => [
        'legend' => [
          'title' => $this->l('Settings'),
          'icon' => 'icon-cogs',
        ],
        'input' => [
          [
            'type' => 'checkbox',
            'label' => $this->l('Enable'),
            'name' => 'INDODANA_ENABLE',
            'values' => [
              'query' => [
                [
                  'id' => 'TRUE',
                  'name' => 'check to enable the plugins',
                  'val' => 1
                ]
              ],
              'id' => 'id',
              'name' => 'name',
            ],
          ],
          [
            'type' => 'text',
            'label' => $this->l('Title'),
            'name' => 'INDODANA_TITLE',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'textarea',
            'label' => $this->l('Description'),
            'name' => 'INDODANA_DESCRIPTION',
            'required' => true
          ],
          [
            'type' => 'select',
            'label' => $this->l('Environment'),
            'desc' => 'Choose "SANDBOX" if you are testing this plugins',
            'name' => 'INDODANA_ENVIRONMENT',
            'required' => true,
            'options' => [
              'query' => [
                [
                  'id_option' => 'SANDBOX',
                  'name' => 'SANDBOX'
                ],
                [
                  'id_option' => 'PRODUCTION',
                  'name' => 'PRODUCTION'
                ],
              ],
              'id' => 'id_option',
              'name' => 'name'
            ]
          ],
          [
            'type' => 'text',
            'label' => $this->l('Sandbox API Key'),
            'desc' => 'Enter sandbox API Key provided by Indodana',
            'name' => 'INDODANA_API_KEY',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Sandbox API Secret'),
            'desc' => 'Enter sandbox API Secret provided by Indodana',
            'name' => 'INDODANA_API_SECRET',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Production API Key'),
            'desc' => 'Enter production API Key provided by Indodana',
            'name' => 'INDODANA_API_KEY_PRODUCTION',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Production API Secret'),
            'desc' => 'Enter production API Secret provided by Indodana',
            'name' => 'INDODANA_API_SECRET_PRODUCTION',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store Name'),
            'name' => 'INDODANA_STORE_NAME',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store URL'),
            'name' => 'INDODANA_STORE_URL',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store Email'),
            'name' => 'INDODANA_STORE_EMAIL',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store Phone'),
            'name' => 'INDODANA_STORE_PHONE',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'select',
            'label' => $this->l('Store Country Code'),
            'name' => 'INDODANA_STORE_COUNTRY_CODE',
            'required' => true,
            'options' => [
              'query' => [
                [
                  'id_option' => 'IDN',
                  'name' => 'IDN'
                ],
              ],
              'id' => 'id_option',
              'name' => 'name'
            ]
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store City'),
            'name' => 'INDODANA_STORE_CITY',
            'size' => 100,
            'required' => true
          ],
          [
            'type' => 'textarea',
            'label' => $this->l('Store Address'),
            'name' => 'INDODANA_STORE_ADDRESS',
            'required' => true
          ],
          [
            'type' => 'text',
            'label' => $this->l('Store Postal Code'),
            'name' => 'INDODANA_STORE_POSTAL_CODE',
            'size' => 5,
            'required' => true
          ],
          [
            'class' => 'form-control',
            'type' => 'select',
            'label' => $this->l('Default Order Pending Status'),
            'name' => 'INDODANA_DEFAULT_ORDER_PENDING_STATUS',
            'required' => true,
            'options' => [
              'query' => $options,
              'id' => 'id_option',
              'name' => 'name'
            ]
          ],
          [
            'type' => 'select',
            'label' => $this->l('Default Order Success Status'),
            'name' => 'INDODANA_DEFAULT_ORDER_SUCCESS_STATUS',
            'required' => true,
            'options' => [
              'query' => $options,
              'id' => 'id_option',
              'name' => 'name'
            ]
          ],
          [
            'type' => 'select',
            'label' => $this->l('Default Order Failed Status'),
            'name' => 'INDODANA_DEFAULT_ORDER_FAILED_STATUS',
            'required' => true,
            'options' => [
              'query' => $options,
              'id' => 'id_option',
              'name' => 'name'
            ]
          ],
        ],
        'submit' => [
          'title' => $this->l('Save'),
        ],
      ],
    ];
  }

  /**
   * Set values for the inputs.
   */
  protected function getConfigFormValues()
  {
    $formValues = [];
    foreach ($this->moduleConfigs as $key => $value) {
      $formValues[$key] = Configuration::get($key);
    }

    return $formValues;
  }

  /**
   * Save form data.
   */
  protected function postProcess()
  {
    $formValues = $this->getConfigFormValues();

    foreach (array_keys($formValues) as $key) {
      $value = Tools::getValue($key);
      if ($key === 'INDODANA_ENABLE_TRUE') {
        $this->checkToggleModuleState($value);
      }

      Configuration::updateValue($key, $value);
    }
  }

  /**
   * This method is used to render the payment button,
   * Take care if the button should be displayed or not.
   */
  public function hookPayment($params)
  {
    $currencyId = $params['cart']->id_currency;
    $currency = new Currency((int)$currencyId);

    if (in_array($currency->iso_code, $this->limited_currencies) == false) {
      return false;
    }

    $this->smarty->assign([
      'module_dir', $this->_path,
      'this_path_bw' => $this->_path,
      'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
    ]);

    return $this->display(__FILE__, 'payment.tpl');
  }

  /**
   * Return payment options available for PS 1.7+
   *
   * @param array Hook parameters
   *
   * @return array|null
   */
  public function hookPaymentOptions($params)
  {
    $cart = $params['cart'];

    if (!$this->active || !$this->checkCurrency($cart) || !$this->checkConfig()) {
      return;
    }

    try {
      $indodanaTools = new IndodanaTools();
      $totalAmount = $indodanaTools->getTotalAmount($cart);
      $installmentOptions = $indodanaTools->getIndodanaCommon()->getInstallmentOptions([
        'totalAmount' => $totalAmount,
        'discountAmount' => $indodanaTools->getTotalDiscountAmount($cart),
        'shippingAmount' => $indodanaTools->getTotalShippingAmount($cart),
        'taxAmount' => $indodanaTools->getTotalTaxAmount($cart),
        'products' => $indodanaTools->getProducts($cart),
        'adminFeeAmount' => $indodanaTools->getAdminFeeAmount($cart),
        'additionalFeeAmount' => $indodanaTools->getAdditionalFeeAmount($cart),
        'insuranceFeeAmount' => $indodanaTools->getInsuranceFeeAmount($cart)
      ]);
    } catch (Exception $e) {
      // hide Indodana payment method when IndodanaCommon return exception
      return;
    }

    $formAction = $this->context->link->getModuleLink($this->name, 'validation', [], true);
    $this->smarty->assign([
      'action' => $formAction,
      'description' => Configuration::get('INDODANA_DESCRIPTION'),
      'installmentOptions' => $installmentOptions,
      'displayName' => $this->displayName,
      'totalAmount' => $totalAmount
    ]);
    $paymentForm = $this->fetch('module:indodana/views/templates/hook/payment_form.tpl');

    $option = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
    $option->setModuleName($this->displayName)
      ->setCallToActionText($this->displayName)
      ->setAction($formAction)
      ->setForm($paymentForm)
      ->setLogo(IndodanaCommon\IndodanaConstant::LOGO_URL);

    return [
      $option
    ];
  }

  private function checkConfig()
  {
    foreach ($this->moduleConfigs as $key => $value) {
      $env = Configuration::get('INDODANA_ENVIRONMENT');

      // skip title check
      if ($key == 'INDODANA_TITLE') {
        continue;
      }

      // skip api key/secret check when environment is different
      if ($env == 'SANDBOX' && ($key == 'INDODANA_API_KEY_PRODUCTION' || $key == 'INDODANA_API_SECRET_PRODUCTION')) {
        continue;
      } elseif ($env == 'PRODUCTION' && ($key == 'INDODANA_API_KEY' || $key == 'INDODANA_API_SECRET')) {
        continue;
      }

      if (empty(Configuration::get($key))) {
        return false;
      }
    }

    return true;
  }

  private function checkCurrency($cart)
  {
    $currencyOrder = new Currency($cart->id_currency);
    $currenciesModule = $this->getCurrency($cart->id_currency);
    if (is_array($currenciesModule)) {
      foreach ($currenciesModule as $currencyModule) {
        if ($currencyOrder->id == $currencyModule['id_currency']) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Displaying payment method for Prestashop 1.6
   */
  public function hookDisplayPayment($params)
  {
    $cart = $params['cart'];
    $currencyId = $cart->id_currency;
    $currency = new Currency((int)$currencyId);

    if (in_array($currency->iso_code, $this->limited_currencies) == false || !$this->checkConfig()) {
      return false;
    }

    try {
      $indodanaTools = new IndodanaTools();
      $totalAmount = $indodanaTools->getTotalAmount($cart);
      $installmentOptions = $indodanaTools->getIndodanaCommon()->getInstallmentOptions([
        'totalAmount' => $totalAmount,
        'discountAmount' => $indodanaTools->getTotalDiscountAmount($cart),
        'shippingAmount' => $indodanaTools->getTotalShippingAmount($cart),
        'taxAmount' => $indodanaTools->getTotalTaxAmount($cart),
        'products' => $indodanaTools->getProducts($cart),
        'adminFeeAmount' => $indodanaTools->getAdminFeeAmount($cart),
        'additionalFeeAmount' => $indodanaTools->getAdditionalFeeAmount($cart),
        'insuranceFeeAmount' => $indodanaTools->getInsuranceFeeAmount($cart)
      ]);
    } catch (Exception $e) {
      // hide Indodana payment method when IndodanaCommon return exception
      return false;
    }

    $this->smarty->assign([
      'module_dir', $this->_path,
      'this_path_bw' => $this->_path,
      'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
      'moduleName' => $this->name,
      'displayName' => $this->displayName,
      'indodanaLogo' => IndodanaCommon\IndodanaConstant::LOGO_URL,
      'totalAmount' => $totalAmount
    ]);

    return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
  }

  /**
   * Displaying order confirmation page for Prestashop 1.6
   */
  public function hookDisplayPaymentReturn($params)
  {
    // Prestsahop 1.7 use default confirmation page
    if (_PS_VERSION_ >= 1.7 || $this->active == false) {
      return;
    }

    $order = $params['objOrder'];

    if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR')) {
      $this->smarty->assign('status', 'ok');
    }

    $this->smarty->assign([
      'id_order' => $order->id,
      'reference' => $order->reference,
      'params' => $params,
      'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
    ]);

    return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
  }

  /**
   * Enable/disable module
   */
  private function checkToggleModuleState($value)
  {
    $old = (bool) Configuration::get('INDODANA_ENABLE_TRUE');
    if ($old !== $value) {
      if ($value) {
        Module::enableByName($this->name);
      } else {
        Module::disableByName($this->name);
      }
    }
  }
}
