<?php

require_once DIR_SYSTEM . 'library/indodana/autoload.php';

use IndodanaCommon\IndodanaConstant;

class ModelPaymentIndodanaCheckout extends Model {
  const MINIMIM_ORDER_AMOUNT = 10000;

  public function getMethod($address, $total) {
    $defaultCurrency = $this->config->get('config_currency');
    $totalInIDR = ceil($this->currency->convert($total, $defaultCurrency, 'IDR'));

    if ($totalInIDR < self::MINIMIM_ORDER_AMOUNT) {
      return null;
    }

    $logoUrl = IndodanaConstant::LOGO_URL;
    $method_data = array(
      'code'        => 'indodana_checkout',
      'title'       => "<img src='${logoUrl}'>",
      'terms'       => '',
      'sort_order'  => $this->config->get('indodana_checkout_sort_order')
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
