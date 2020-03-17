<?php
class ModelPaymentIndodanaCheckout extends Model {
    const MINIMIM_ORDER_AMOUNT = 10000;

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

  private function getOrderTotalRows($order_id, $codes)
  {
    $in_codes = implode("','", $codes);

    return $this->db->query(
      "SELECT `title`, `value` FROM `". DB_PREFIX . "order_total` WHERE `order_id` = " . (int) $order_id . " AND `code` IN ('" . $in_codes . "')"
    )->rows;
  }

  public function getShippingRows($order_id) {
    return $this->getOrderTotalRows($order_id, ['shipping']);
  }

  public function getTaxRows($order_id)
  {
    return $this->getOrderTotalRows($order_id, ['tax']);
  }

  public function getDiscountRows($order_id)
  {
    return $this->getOrderTotalRows($order_id, ['reward', 'coupon', 'voucher']);
  }

  public function getTotalRows($order_id)
  {
    return $this->getOrderTotalRows($order_id, ['total']);
  }
}
