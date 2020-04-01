<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\ApiResources\GetInstallmentOptions;
use IndodanaCommon\TestData;
use IndodanaCommon\Seller;

class GetInstallmentOptionsTest extends TestCase
{
  public function testInstantiatingWithValidArgumentsReturnProperResult()
  {
    $getInstallmentOptions = new GetInstallmentOptions(
      TestData::GET_INSTALLMENT_OPTIONS,
      new Seller(TestData::SELLER)
    );

    $payload = $getInstallmentOptions->getPayload();

    $this->assertArrayHasKey('amount', $payload);
    $this->assertArrayHasKey('items', $payload);
  }
}
