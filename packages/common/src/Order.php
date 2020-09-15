<?php

namespace IndodanaCommon;

use Respect\Validation\Validator;
use Indodana\RespectValidation\RespectValidationHelper;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaConstant;
use IndodanaCommon\Seller;

Validator::with('IndodanaCommon\\Validation\\Rules');

class Order
{
  const DEFAULT_ITEM_PARENT_TYPE = 'SELLER';
  const SHIPPING_FEE_ITEM_ID = 'shippingfee';
  const TAX_FEE_ITEM_ID = 'taxfee';
  const DISCOUNT_ITEM_ID = 'discount';
  const ADMIN_FEE_ITEM_ID = 'adminfee';
  const ADDITIONAL_FEE_ITEM_ID = 'additionalfee';
  const INSURANCE_FEE_ITEM_ID = 'insurancefee';

  private $amount;
  private $items;

  public function __construct(array $input = [], Seller $seller)
  {
    $validator = Validator::create()
      ->key('totalAmount', Validator::numberType()->notOptional())
      ->key('products', Validator::arrayType()->notEmpty())
      ->key('shippingAmount', Validator::numberType()->notOptional())
      ->key('taxAmount', Validator::numberType()->notOptional())
      ->key('discountAmount', Validator::numberType()->notOptional())
      ->key('adminFeeAmount', Validator::numberType(), false)
      ->key('additionalFeeAmount', Validator::numberType(), false)
      ->key('insuranceFeeAmount', Validator::numberType(), false);

    $validationResult = RespectValidationHelper::validate($validator, $input);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaCommonException($validationResult->printErrorMessages());
    }

    $this->amount = $input['totalAmount'];

    $adminFeeAmount = isset($input['adminFeeAmount']) ? $input['adminFeeAmount'] : 0;
    $additionalFeeAmount = isset($input['additionalFeeAmount']) ? $input['additionalFeeAmount'] : 0;
    $insuranceFeeAmount = isset($input['insuranceFeeAmount']) ? $input['insuranceFeeAmount'] : 0;

    $this->items = $this->createItems(
      $input['products'],
      $input['shippingAmount'],
      $input['taxAmount'],
      $input['discountAmount'],
      $adminFeeAmount,
      $additionalFeeAmount,
      $insuranceFeeAmount,
      $seller
    );
  }

  private function getShippingFee($shippingAmount)
  {
    return [
      'id' => self::SHIPPING_FEE_ITEM_ID,
      'url' => '',
      'name' => 'Shipping Fee',
      'price' => (float) abs($shippingAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function getTaxFee($taxAmount)
  {
    return [
      'id' => self::TAX_FEE_ITEM_ID,
      'url' => '',
      'name' => 'Tax Fee',
      'price' => (float) abs($taxAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function getDiscount($discountAmount)
  {
    return [
      'id' => self::DISCOUNT_ITEM_ID,
      'url' => '',
      'name' => 'Discount',
      'price' => (float) abs($discountAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function getAdminFee($adminFeeAmount)
  {
    return [
      'id' => self::ADMIN_FEE_ITEM_ID,
      'url' => '',
      'name' => 'Admin Fee',
      'price' => (float) abs($adminFeeAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function getAdditionalFee($additionalFeeAmount)
  {
    return [
      'id' => self::ADDITIONAL_FEE_ITEM_ID,
      'url' => '',
      'name' => 'Additional Fee',
      'price' => (float) abs($additionalFeeAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function getInsuranceFee($insuranceFeeAmount)
  {
    return [
      'id' => self::INSURANCE_FEE_ITEM_ID,
      'url' => '',
      'name' => 'Insurance Fee',
      'price' => (float) abs($insuranceFeeAmount),
      'type' => '',
      'category' => IndodanaConstant::DEFAULT_ITEM_CATEGORY,
      'quantity' => 1
    ];
  }

  private function createItems(
    $products,
    $shippingAmount,
    $taxAmount,
    $discountAmount,
    $adminFeeAmount,
    $additionalFeeAmount,
    $insuranceFeeAmount,
    $seller
  ) {
    $shippingFee = $this->getShippingFee($shippingAmount);
    $taxFee = $this->getTaxFee($taxAmount);
    $discount = $this->getDiscount($discountAmount);
    $adminFee = $this->getAdminFee($adminFeeAmount);
    $additionalFee = $this->getAdditionalFee($additionalFeeAmount);
    $insuranceFee = $this->getInsuranceFee($insuranceFeeAmount);

    $items = array_merge($products, [
      $shippingFee,
      $taxFee,
      $discount,
      $adminFee,
      $additionalFee,
      $insuranceFee,
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
