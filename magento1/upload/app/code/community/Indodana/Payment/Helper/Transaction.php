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
        $transactionObjects[] = $shippingCostObject;

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
        $shippingDescription = $cart->getShippingAddress()->getData('shipping_description');
        $shippingAmount = $cart->getShippingAddress()->getShippingAmount();
        $shippingObject = array(
            'id'        => 'shippingfee',
            'url'       => '',
            'name'      => $shippingDescription,
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