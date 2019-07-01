<?php
class ModelPaymentIndodanaCheckout extends Model {
    public function getMethod($address, $total) {
        $this->load->language('payment/custom');
      
        $method_data = array(
          'code'     => 'indodana_checkout',
          'title'    => 'Indodana Paylater'
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