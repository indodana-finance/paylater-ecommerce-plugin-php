<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\ApiResources\Checkout;
use IndodanaCommon\TestData;
use IndodanaCommon\Seller;

class CheckoutTest extends TestCase
{
  private $postalCode = '11111';

  public function testInstantiatingWithoutBillingAddressPostalCodeWillUseDefaultValue()
  {
    $checkout = new Checkout(
      TestData::CHECKOUT,
      new Seller(TestData::SELLER)
    );

    $payload = $checkout->getPayload();

    $this->assertArrayHasKey('billingAddress', $payload);

    $billingAddress = $payload['billingAddress'];
    $this->assertArrayHasKey('postalCode', $billingAddress);
    $this->assertSame(
      Checkout::DEFAULT_POSTAL_CODE,
      $billingAddress['postalCode']
    );
  }

  public function testInstantiatingWithBillingAddressPostalCodeWillUseThatValue()
  {
    $input = TestData::CHECKOUT;
    $input['billingAddress'] = [
      'postalCode' => $this->postalCode
    ];

    $checkout = new Checkout(
      $input,
      new Seller(TestData::SELLER)
    );

    $payload = $checkout->getPayload();

    $this->assertArrayHasKey('billingAddress', $payload);

    $billingAddress = $payload['billingAddress'];
    $this->assertArrayHasKey('postalCode', $billingAddress);
    $this->assertSame(
      $this->postalCode,
      $billingAddress['postalCode']
    );
  }

  public function testInstantiatingWithoutShippingAddressPostalCodeWillUseDefaultValue()
  {
    $checkout = new Checkout(
      TestData::CHECKOUT,
      new Seller(TestData::SELLER)
    );

    $payload = $checkout->getPayload();

    $this->assertArrayHasKey('shippingAddress', $payload);

    $shippingAddress = $payload['shippingAddress'];
    $this->assertArrayHasKey('postalCode', $shippingAddress);
    $this->assertSame(
      Checkout::DEFAULT_POSTAL_CODE,
      $shippingAddress['postalCode']
    );
  }

  public function testInstantiatingWithShippingAddressPostalCodeWillUseThatValue()
  {
    $input = TestData::CHECKOUT;
    $input['shippingAddress'] = [
      'postalCode' => $this->postalCode
    ];

    $checkout = new Checkout(
      $input,
      new Seller(TestData::SELLER)
    );

    $payload = $checkout->getPayload();

    $this->assertArrayHasKey('shippingAddress', $payload);

    $shippingAddress = $payload['shippingAddress'];
    $this->assertArrayHasKey('postalCode', $shippingAddress);
    $this->assertSame(
      $this->postalCode,
      $shippingAddress['postalCode']
    );
  }

  public function testInstantiatingWithCompleteArgumentsReturnCompletePayload()
  {
    $checkout = new Checkout(
      TestData::CHECKOUT_COMPLETE,
      new Seller(TestData::SELLER)
    );

    $payload = $checkout->getPayload();

    $this->assertArrayHasKey('transactionDetails', $payload);

    $transactionDetails = $payload['transactionDetails'];
    $this->assertArrayHasKey('merchantOrderId', $transactionDetails);
    $this->assertArrayHasKey('amount', $transactionDetails);
    $this->assertArrayHasKey('items', $transactionDetails);

    $this->assertArrayHasKey('customerDetails', $payload);
    $this->assertArrayHasKey('sellers', $payload);
    $this->assertArrayHasKey('billingAddress', $payload);
    $this->assertArrayHasKey('shippingAddress', $payload);
    $this->assertArrayHasKey('paymentType', $payload);
    $this->assertArrayHasKey('approvedNotificationUrl', $payload);
    $this->assertArrayHasKey('cancellationRedirectUrl', $payload);
    $this->assertArrayHasKey('backToStoreUrl', $payload);
  }
}
