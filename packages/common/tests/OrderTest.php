<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\Seller;
use IndodanaCommon\Order;

class OrderTest extends TestCase
{
  private $input = [
    'totalAmount' => 412000,
    'products' => [
      [
        'id' => 'MAC1',
        'name' => 'MacBook Pro',
        'price' => 406000,
        'quantity' => 1,
      ]
    ],
    'shippingAmount' => 10000,
    'taxAmount' => 20000,
    'discountAmount' => 24000,
  ];
  private $sellerId = 'testid';

  protected $sellerMock;

  protected function setUp()
  {
    $this->sellerMock = Mockery::mock(Seller::class);
    $this->sellerMock
         ->expects()
         ->getId()
         ->andReturn($this->sellerId);
  }

  public function testInstantiatingWithUnsetTotalAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    unset($input['totalAmount']);
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithInvalidTotalAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['totalAmount'] = 'test';
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithUnsetProductsThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    unset($input['products']);
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithEmptyProductsThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['products'] = [];
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithInvalidProductsThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['products'] = 'test';
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithUnsetShippingAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    unset($input['shippingAmount']);
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithInvalidShippingAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['shippingAmount'] = 'test';
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithUnsetTaxAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    unset($input['taxAmount']);
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithInvalidTaxAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['taxAmount'] = 'test';
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithUnsetDiscountAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    unset($input['discountAmount']);
    new Order($input, $this->sellerMock);
  }

  public function testInstantiatingWithInvalidDiscountAmountThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $input = $this->input;
    $input['discountAmount'] = 'test';
    new Order($input, $this->sellerMock);
  }

  private function getIndexOfItemById($id, $items)
  {
    return array_search($id, array_column($items, 'id'));
  }

  public function testInstantiatingWithValidArgumentsReturnsProperResult()
  {
    $input = $this->input;

    $order = new Order($input, $this->sellerMock);

    $amount = $order->getAmount();

    // Order's amount should be the same as input's totalAmount
    $this->assertSame(
      $amount,
      $input['totalAmount']
    );

    $items = $order->getItems();

    // Check parentType and parentId of each Order's item
    foreach ($items as $item) {
      $this->assertSame(
        $item['parentType'],
        Order::DEFAULT_ITEM_PARENT_TYPE
      );

      $this->assertSame(
        $item['parentId'],
        $this->sellerId
      );
    }

    // Check shipping object
    $shippingObjectIndex = $this->getIndexOfItemById(Order::SHIPPING_ITEM_ID, $items);

    $this->assertNotEmpty($shippingObjectIndex);
    $this->assertEquals(
      $items[$shippingObjectIndex]['price'],
      $input['shippingAmount']
    );

    // Check tax object
    $taxObjectIndex = $this->getIndexOfItemById(Order::TAX_ITEM_ID, $items);

    $this->assertNotEmpty($taxObjectIndex);
    $this->assertEquals(
      $items[$taxObjectIndex]['price'],
      $input['taxAmount']
    );

    // Check discount object
    $discountObjectIndex = $this->getIndexOfItemById(Order::DISCOUNT_ITEM_ID, $items);

    $this->assertNotEmpty($discountObjectIndex);
    $this->assertEquals(
      $items[$discountObjectIndex]['price'],
      $input['discountAmount']
    );
  }
}
