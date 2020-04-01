<?php

use PHPUnit\Framework\TestCase;
use Indodana\Indodana;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\Utils;
use IndodanaCommon\Seller;
use IndodanaCommon\TestData;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IndodanaServiceTest extends TestCase
{
  protected function setUp()
  {
    // IndodanaLogger will not be doing anything
    $indodanaLoggerMock = Mockery::mock('alias:IndodanaCommon\IndodanaLogger');
    $indodanaLoggerMock
      ->expects()
      ->info(Mockery::type('string'));
    $indodanaLoggerMock
      ->expects()
      ->warning(Mockery::type('string'));
    $indodanaLoggerMock
      ->expects()
      ->error(Mockery::type('string'));

    $this->indodanaMock = Mockery::mock(Indodana::class);
    $this->seller = new Seller(TestData::SELLER);
    $this->authUtilMock = Mockery::mock(Utils\Auth::class);
  }

  public function testGetIndodanaReturnsDesiredOutput()
  {
    $indodanaService = new IndodanaService(
      $this->indodanaMock,
      $this->seller,
      $this->authUtilMock
    );

    $this->assertSame(
      $this->indodanaMock,
      $indodanaService->getIndodana()
    );
  }

  public function testGetInstallmentOptionsReturnsDesiredOutput()
  {
    $desiredOutput = [];

    $this->indodanaMock
         ->expects()
         ->getInstallmentOptions(Mockery::type('array'))
         ->andReturn($desiredOutput);

    $indodanaService = new IndodanaService(
      $this->indodanaMock,
      $this->seller,
      $this->authUtilMock
    );

    $actualOutput = $indodanaService->getInstallmentOptions(TestData::GET_INSTALLMENT_OPTIONS);

    $this->assertSame(
      $desiredOutput,
      $actualOutput
    );
  }

  public function testCheckoutReturnsDesiredOutput()
  {
    $desiredOutput = [];

    $this->indodanaMock
         ->expects()
         ->checkout(Mockery::type('array'))
         ->andReturn($desiredOutput);

    $indodanaService = new IndodanaService(
      $this->indodanaMock,
      $this->seller,
      $this->authUtilMock
    );

    $actualOutput = $indodanaService->checkout(TestData::CHECKOUT);

    $this->assertSame(
      $desiredOutput,
      $actualOutput
    );
  }

  public function testIsValidAuthTokenWithEmptyBearerCredentialsReturnsFalse()
  {
    $this->authUtilMock
         ->expects()
         ->getBearerCredentials(Mockery::type('string'))
         ->andReturn(null);

    $indodanaService = new IndodanaService(
      $this->indodanaMock,
      $this->seller,
      $this->authUtilMock
    );

    $this->assertFalse(
      $indodanaService->isValidAuthToken('')
    );
  }

  public function testIsValidAuthTokenWithInvalidIndodanaAuthCredentialsReturnFalse()
  {
    $credentials = 'test:test';

    $this->indodanaMock
         ->expects()
         ->validateAuthCredentials(Mockery::type('string'))
         ->andReturn(false);

    $this->authUtilMock
         ->expects()
         ->getBearerCredentials(Mockery::type('string'))
         ->andReturn($credentials);

    $indodanaService = new IndodanaService(
      $this->indodanaMock,
      $this->seller,
      $this->authUtilMock
    );

    $this->assertFalse(
      $indodanaService->isValidAuthToken("Bearer ${credentials}")
    );
  }
}
