<?php
class ControllerPaymentIndodanaCheckout extends Controller {
    private $errors = array();

    public function index() {
        $this->load_model();
        /* 
            In OPENCART, every successful submit through the form will be redirected to the same page
            with the value from the previous form sent as POST data.

            We need to check if 
            1. the received request contains the previous form's data (apply the data and redirect to home page)
            2. doesn't contain the previous form's data (give user the form)
        */
        if ($this->receive_configuration_data() && $this->validate()) {
            $this->apply_configuration();
            $this->redirect_to_extension_page();
        }
        $this->initialize_aditional_data();
        $this->load_errors();
        $this->apply_default_value();
        $this->initialize_view();

		$this->response->setOutput($this->render());
    }

    private function load_model() {
        $this->language->load('payment/indodana_checkout');
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    }

    private function redirect_to_extension_page() {
        $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token']));
    }

    private function apply_configuration() {
        $this->model_setting_setting->editSetting('indodana_checkout', $this->request->post);
        $this->session->data['success'] = $this->language->get('text_success');
    }

    private function receive_configuration_data() {
        return ($this->request->server['REQUEST_METHOD'] == 'POST');
    }

    private function initialize_view() {
        $this->template = 'payment/indodana_checkout.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
    }

        /*
        When user press EDIT or ADD, we need to show the previous data to the user
        This function will get all the form's data that might have been saved before, and show it
    */
    private function apply_default_value() {
        if (isset($this->request->post['indodana_checkout_first_name'])) {
            $this->data['indodana_checkout_first_name'] = $this->request->post['indodana_checkout_first_name'];
        } else {
            $this->data['indodana_checkout_first_name'] = $this->config->get('indodana_checkout_first_name');
        }

        if (isset($this->request->post['indodana_checkout_last_name'])) {
            $this->data['indodana_checkout_last_name'] = $this->request->post['indodana_checkout_last_name'];
        } else {
            $this->data['indodana_checkout_last_name'] = $this->config->get('indodana_checkout_last_name');
        }

        if (isset($this->request->post['indodana_checkout_address'])) {
            $this->data['indodana_checkout_address'] = $this->request->post['indodana_checkout_address'];
        } else {
            $this->data['indodana_checkout_address'] = $this->config->get('indodana_checkout_address');
        }

        if (isset($this->request->post['indodana_checkout_city'])) {
            $this->data['indodana_checkout_city'] = $this->request->post['indodana_checkout_city'];
        } else {
            $this->data['indodana_checkout_city'] = $this->config->get('indodana_checkout_city');
        }

        if (isset($this->request->post['indodana_checkout_postal_code'])) {
            $this->data['indodana_checkout_postal_code'] = $this->request->post['indodana_checkout_postal_code'];
        } else {
            $this->data['indodana_checkout_postal_code'] = $this->config->get('indodana_checkout_postal_code');
        }

        if (isset($this->request->post['indodana_checkout_phone'])) {
            $this->data['indodana_checkout_phone'] = $this->request->post['indodana_checkout_phone'];
        } else {
            $this->data['indodana_checkout_phone'] = $this->config->get('indodana_checkout_phone');
        }

        if (isset($this->request->post['indodana_checkout_default_country_code'])) {
            $this->data['indodana_checkout_default_country_code'] = $this->request->post['indodana_checkout_default_country_code'];
        } else {
            $this->data['indodana_checkout_default_country_code'] = $this->config->get('indodana_checkout_default_country_code');
        }

        if (isset($this->request->post['indodana_checkout_api_secret'])) {
            $this->data['indodana_checkout_api_secret'] = $this->request->post['indodana_checkout_api_secret'];
        } else {
            $this->data['indodana_checkout_api_secret'] = $this->config->get('indodana_checkout_api_secret');
        }

        if (isset($this->request->post['indodana_checkout_api_key'])) {
            $this->data['indodana_checkout_api_key'] = $this->request->post['indodana_checkout_api_key'];
        } else {
            $this->data['indodana_checkout_api_key'] = $this->config->get('indodana_checkout_api_key');
        }

        if (isset($this->request->post['indodana_checkout_environment'])) {
            $this->data['indodana_checkout_environment'] = $this->request->post['indodana_checkout_environment'];
        } else {
            $this->data['indodana_checkout_environment'] = $this->config->get('indodana_checkout_environment');
        }

        if (isset($this->request->post['indodana_checkout_default_order_status_id'])) {
            $this->data['indodana_checkout_default_order_status_id'] = $this->request->post['indodana_checkout_default_order_status_id']; 
        } else {
            $this->data['indodana_checkout_default_order_status_id'] = $this->config->get('indodana_checkout_default_order_status_id');
        }

        if (isset($this->request->post['indodana_checkout_status'])) {
            $this->data['indodana_checkout_status'] = $this->request->post['indodana_checkout_status'];
        } else {
            $this->data['indodana_checkout_status'] = $this->config->get('indodana_checkout_status');
        }

        if (isset($this->request->post['indodana_checkout_sort_order'])) {
            $this->data['indodana_checkout_sort_order'] = $this->request->post['indodana_checkout_sort_order'];
        } else {
            $this->data['indodana_checkout_sort_order'] = $this->config->get('indodana_checkout_sort_order');
        }
    }

    private function initialize_aditional_data() {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_success'] = $this->language->get('text_success');
        $this->data['text_button_save'] = $this->language->get('text_button_save');
        $this->data['text_button_cancel'] = $this->language->get('text_button_cancel');

        $this->data['text_environment_sandbox'] = $this->language->get('text_environment_sandbox');
        $this->data['text_environment_production'] = $this->language->get('text_environment_production');

        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_first_name'] = $this->language->get('entry_first_name');
        $this->data['entry_last_name'] = $this->language->get('entry_last_name');
        $this->data['entry_address'] = $this->language->get('entry_address');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_postal_code'] = $this->language->get('entry_postal_code');
        $this->data['entry_phone'] = $this->language->get('entry_phone');
        $this->data['entry_country_code'] = $this->language->get('entry_country_code');
        $this->data['entry_api_secret'] = $this->language->get('entry_api_secret');
        $this->data['entry_api_key'] = $this->language->get('entry_api_key');
        $this->data['entry_environment'] = $this->language->get('entry_environment');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['country_codes'] = $this->language->get('country_codes');
        $this->data['environment_sandbox'] = $this->language->get('environment_sandbox');
        $this->data['environment_production'] = $this->language->get('environment_production');
        
		$this->data['form_action'] = $this->url->link('payment/indodana_checkout', 'token=' . $this->session->data['token']);
        $this->data['form_cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token']);

        $this->data['errors'] = $this->errors;
    }

    private function load_errors() {
        if (isset($this->errors['first_name_empty'])) {
			$this->data['error_first_name_empty'] = $this->errors['first_name_empty'];
		} else {
			$this->data['error_first_name_empty'] = '';
        }

        if (isset($this->errors['address_empty'])) {
			$this->data['error_address_empty'] = $this->errors['address_empty'];
		} else {
			$this->data['error_address_empty'] = '';
        }

        if (isset($this->errors['city_empty'])) {
			$this->data['error_city_empty'] = $this->errors['city_empty'];
		} else {
			$this->data['error_city_empty'] = '';
        }
        
        if (isset($this->errors['postal_code_empty'])) {
			$this->data['error_postal_code_empty'] = $this->errors['postal_code_empty'];
		} else {
			$this->data['error_postal_code_empty'] = '';
        }

        if (isset($this->errors['phone_empty'])) {
			$this->data['error_phone_empty'] = $this->errors['phone_empty'];
		} else {
			$this->data['error_phone_empty'] = '';
        }

        if (isset($this->errors['country_code_empty'])) {
			$this->data['error_country_code_empty'] = $this->errors['country_code_empty'];
		} else {
			$this->data['error_country_code_empty'] = '';
        }

        if (isset($this->errors['api_secret_empty'])) {
			$this->data['error_api_secret_empty'] = $this->errors['api_secret_empty'];
		} else {
			$this->data['error_api_secret_empty'] = '';
        }
        
        if (isset($this->errors['api_key_empty'])) {
			$this->data['error_api_key_empty'] = $this->errors['api_key_empty'];
		} else {
			$this->data['error_api_key_empty'] = '';
        }
        
        if (isset($this->errors['environment'])) {
            $this->data['error_environment_empty'] = $this->errors['environment_empty'];
        } else {
            $this->data['error_environment_empty'] = '';
        }

        if (isset($this->errors['sort_order'])) {
            $this->data['error_sort_order_empty'] = $this->errors['sort_order'];
        } else {
            $this->data['error_sort_order_empty'] = '';
        }
    }

    private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/indodana_checkout')) {
			$this->errors['permission'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['indodana_checkout_first_name'])) {
            $this->errors['first_name_empty'] = $this->language->get('error_first_name_empty');
        }

        if (empty($this->request->post['indodana_checkout_address'])) {
            $this->errors['address_empty'] = $this->language->get('error_address_empty');
        }

        if (empty($this->request->post['indodana_checkout_city'])) {
            $this->errors['city_empty'] = $this->language->get('error_city_empty');
        }

        if (empty($this->request->post['indodana_checkout_postal_code'])) {
            $this->errors['postal_code_empty'] = $this->language->get('error_postal_code_empty');
        }

        if (empty($this->request->post['indodana_checkout_phone'])) {
            $this->errors['phone_empty'] = $this->language->get('error_phone_empty');
        }

        if (empty($this->request->post['indodana_checkout_default_country_code'])) {
            $this->errors['country_code_empty'] = $this->language->get('error_country_code_empty');
        }

        if (empty($this->request->post['indodana_checkout_api_secret'])) {
            $this->errors['api_secret_empty'] = $this->language->get('error_api_secret_empty');
        }

        if (empty($this->request->post['indodana_checkout_api_key'])) {
            $this->errors['api_key_empty'] = $this->language->get('error_api_key_empty');
        }

        if (empty($this->request->post['indodana_checkout_environment'])) {
            $this->errors['environment'] = $this->language->get('error_environment_empty');
        }

        if (empty($this->request->post['indodana_checkout_sort_order'])) {
            $this->errors['sort_order'] = $this->language->get('error_sort_order_empty');
        }

        return empty($this->errors);
    }
}