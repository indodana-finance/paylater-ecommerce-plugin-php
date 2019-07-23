<?php
class ModelPaymentIndodanaCheckout extends Model {
    const MINIMIM_ORDER_AMOUNT = 500000;

    public function getMethod($address, $total) {
        $defaultCurrency = $this->config->get('config_currency');
        $totalInIDR = ceil($this->currency->convert($total, $defaultCurrency, 'IDR'));

        if ($totalInIDR < self::MINIMIM_ORDER_AMOUNT) {
            return null;
        } 

        $method_data = array(
          'code'     => 'indodana_checkout',
          'title'    => '&nbsp&nbsp<img src="https://afpi.or.id/fm/Members/indodana_logo_4500-x-1000.png" height="25" width="112">&nbsp&nbsp',
          'sort_order' => $this->config->get('indodana_checkout_sort_order')
        );
      
        return $method_data;
    }

    public function getShippingDetail($orderId) {
        return $this->db->query("SELECT `title`, `value` FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = " . (int) $orderId . " AND `code` = 'shipping'")->row;
    }

    public function getTaxes($orderId) {
        return $this->db->query("SELECT `title`, `value` FROM `". DB_PREFIX . "order_total` WHERE `order_id` = " . (int) $orderId . " AND `code` = 'tax'");
    }

    public function getAdditionalFee() {
        
    }
}