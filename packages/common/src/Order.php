<?php

namespace IndodanaCommon;

use Respect\Validation\Validator;
use Indodana\RespectValidation\RespectValidationHelper;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\Seller;

Validator::with('IndodanaCommon\\Validation\\Rules');

class Order
{
  const DEFAULT_ITEM_PARENT_TYPE = 'SELLER';
  const SHIPPING_ITEM_ID = 'shippingfee';
  const TAX_ITEM_ID = 'taxfee';
  const DISCOUNT_ITEM_ID = 'discount';

  private $amount;
  private $items;

  public function __construct(array $input = [], Seller $seller)
  {
    $validator = Validator::create()
      ->key('totalAmount', Validator::numberType()->notOptional())
      ->key('products', Validator::arrayType()->notEmpty())
      ->key('shippingAmount', Validator::numberType()->notOptional())
      ->key('taxAmount', Validator::numberType()->notOptional())
      ->key('discountAmount', Validator::numberType()->notOptional());

    $validationResult = RespectValidationHelper::validate($validator, $input);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaCommonException($validationResult->printErrorMessages());
    }

    $this->amount = $input['totalAmount'];
    $this->items = $this->createItems(
      $input['products'],
      $input['shippingAmount'],
      $input['taxAmount'],
      $input['discountAmount'],
      $seller
    );
  }

  private function getShippingFee($shippingAmount) {
    return [
      'id' => self::SHIPPING_ITEM_ID,
      'url' => '',
      'name' => 'Shipping Fee',
      'price' => (float) abs($shippingAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function getTaxFee($taxAmount) {
    return [
      'id' => self::TAX_ITEM_ID,
      'url' => '',
      'name' => 'Tax Fee',
      'price' => (float) abs($taxAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function getDiscount($discountAmount) {
    return [
      'id' => self::DISCOUNT_ITEM_ID,
      'id' => 'discount',
      'url' => '',
      'name' => 'Discount',
      'price' => (float) abs($discountAmount),
      'type' => '',
      'quantity' => 1
    ];
  }

  private function createItems(
    $products,
    $shippingAmount,
    $taxAmount,
    $discountAmount,
    $seller
  ) {
    $shippingFee = $this->getShippingFee($shippingAmount);
    $taxFee = $this->getTaxFee($taxAmount);
    $discount = $this->getDiscount($discountAmount);

    $items = array_merge($products, [
      $shippingFee,
      $taxFee,
      $discount
    ]);

    // Add seller id for each item
    foreach($items as &$item) {
      $item['parentType'] = self::DEFAULT_ITEM_PARENT_TYPE;
      $item['parentId'] = $seller->getId();
    }

    return $items;
  }

  public function getAmount()
  {
    return $this->amount;
  }

  public function getItems()
  {
    return $this->items;
  }
}
