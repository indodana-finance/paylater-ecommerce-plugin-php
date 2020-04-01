<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\Seller;

class SellerTest extends TestCase
{
  public function testInstantiatingWithoutUrlThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    new Seller();
  }

  public function testInstantiatingWithUrlReturnsProperResult()
  {
    $url = 'www.example.com';

    $seller = new Seller([
      'url' => 'www.example.com'
    ]);

    $this->assertInternalType('string', $seller->getId());

    $payload = $seller->getPayload();

    $this->assertArrayHasKey('id', $payload);
    $this->assertArrayHasKey('url', $payload);
  }
}
