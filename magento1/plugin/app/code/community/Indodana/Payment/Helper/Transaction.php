<?php

class Indodana_Payment_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    public function getTransactionObjects($cart)
    {
        $productObjects = $this->getProductObjects($cart);
        $taxObject = $this->getTaxObject($cart);
        $shippingCostObject = $this->getShippingCostObject($cart);
        $discountObject = $this->getDiscountObject($cart);

        $transactionObjects = array();
        $transactionObjects = array_merge($transactionObjects, $productObjects);
        $transactionObjects[] = $taxObject;

        if ($shippingCostObject != null) {
            $transactionObjects[] = $shippingCostObject;
        }

        if ($discountObject != null) {
            $transactionObjects[] = $discountObject;
        }

        return $transactionObjects;
    }

    private function getProductObjects($cart)
    {
        $productObjects = array();
        foreach($cart->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productObject = array(
                'id'        => $product->getId(),
                'url'       => '',
                'name'      => $product->getName(),
                'price'     => $product->getPrice(),
                'type'      => '',
                'quantity'  => $item->getQty()
            );
            array_push($productObjects, $productObject);
        }

        return $productObjects;
    }

    private function getDiscountObject($cart) {
      $discountAmountTotal = 0;

      foreach ($cart->getAllItems() as $item){
          $discountAmountTotal += $item->getDiscountAmount();
      }

      $discountObject = array(
            'id'        => 'discount',
            'url'       => '',
            'name'      => 'Discount',
            'price'     => abs(ceil($discountAmountTotal)),
            'type'      => '',
            'quantity'  => 1
      );

      return $discountObject;
    }

    private function getTaxObject($cart)
    {
        $taxAmount = $cart->getTaxAmount();
        $taxObject = array(
            'id'        => 'taxfee',
            'url'       => '',
            'name'      => 'Tax Fee',
            'price'     => ceil($taxAmount),
            'type'      => '',
            'quantity'  => 1
        );

        return $taxObject;
    }

    private function getShippingCostObject($cart)
    {
        if (is_bool($cart->getShippingAddress())) {
            return null;
        }

        $shippingAmount = $cart->getShippingAddress()->getShippingAmount();

        if ($shippingAmount == null) {
            return null;
        }

        $shippingObject = array(
            'id'        => 'shippingfee',
            'url'       => '',
            'name'      => 'Shipping Fee',
            'price'     => $shippingAmount,
            'type'      => '',
            'quantity'  => 1
        );
        
        return $shippingObject;
    }

    public function calculateTotalPrice($itemObjects)
    {
      $total_price = 0;
      $price_cut_ids = ['discount'];

      foreach($itemObjects as $transactionObject) {
        $this_transaction_total_price = $transactionObject['price'] * $transactionObject['quantity'];

        if (in_array($transactionObject['id'], $price_cut_ids)) {
          $total_price -= $this_transaction_total_price;

          continue;
        }

        $total_price += $this_transaction_total_price;
      }

      return $total_price;
    }
}
