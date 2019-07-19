<?php

class Indodana_Payment_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    public function getTransactionObjects($cart)
    {
        $productObjects = $this->getProductObjects($cart);
        $taxObject = $this->getTaxObject($cart);
        $shippingCostObject = $this->getShippingCostObject($cart);

        $transactionObjects = array();
        $transactionObjects = array_merge($transactionObjects, $productObjects);
        $transactionObjects[] = $taxObject;

        if ($shippingCostObject != null) {
            $transactionObjects[] = $shippingCostObject;
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

    public function calculateTotalPrice($transactionObjects)
    {
        $total = 0;
        foreach($transactionObjects as $transactionObject) {
            $total += $transactionObject['price'] * $transactionObject['quantity'];
        }

        return $total;
    }
}