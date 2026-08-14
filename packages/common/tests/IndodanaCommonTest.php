<?php

use PHPUnit\Framework\TestCase;
use Indodana\Indodana;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaCommon;
use IndodanaCommon\IndodanaService;
use IndodanaCommon\IndodanaSentry;
use IndodanaCommon\TestData;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IndodanaCommonTest extends TestCase
{
  protected function setUp()
  {
    // IndodanaLogger will not be doing anything
    $indodanaLoggerMock = Mockery::mock('alias:IndodanaCommon\IndodanaLoggerMock');
    $indodanaLoggerMock
      ->expects()
      ->info(Mockery::type('string'));
    $indodanaLoggerMock
      ->expects()
      ->warning(Mockery::type('string'));
    $indodanaLoggerMock
      ->expects()
      ->error(Mockery::type('string'));
  }

  public function testInstantiatingWithoutSellerThrows()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage(IndodanaCommon::CONSTRUCTOR_ERROR_MESSAGE);

    $config = TestData::INDODANA_COMMON;
    unset($config['seller']);

    new IndodanaCommon($config);
  }

  public function testInstantiatingWithProperArgumentsWillSucceed()
  {
    $this->assertInstanceOf(
      IndodanaCommon::class,
      new IndodanaCommon(TestData::INDODANA_COMMON)
    );
  }

  public function testValidateConfigurationWillSucceed()
  {
    $this->assertArrayHasKey(
      'errors',
      IndodanaCommon::validateConfiguration(TestData::CONFIGURATION)
    );
  }

  public function testGetInstallmentOptionsWithoutErrorsWillSucceed()
  {
    $expectedOutput = [ 'test' => 'test' ];

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getInstallmentOptions(Mockery::type('array'), Mockery::type('string'))
      ->andReturn([ 'payments' => $expectedOutput ]);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $actualOutput = $indodanaCommon->getInstallmentOptions(TestData::GET_INSTALLMENT_OPTIONS);

    $this->assertSame(
      $expectedOutput,
      $actualOutput
    );
  }

  public function testGetInstallmentOptionsWithErrorsThrows()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage(IndodanaCommon::GET_INSTALLMENT_OPTIONS_ERROR_MESSAGE);
    $expectedOutput = [ 'test' => 'test' ];

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getInstallmentOptions(Mockery::type('array'), Mockery::type('string'))
      ->andThrow(new IndodanaCommonException);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $indodanaCommon->getInstallmentOptions(TestData::GET_INSTALLMENT_OPTIONS);
  }

  public function testCheckoutWithoutErrorsWillSucceed()
  {
    $expectedOutput = 'some_url';

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->checkout(Mockery::type('array'), Mockery::type('string'))
      ->andReturn([ 'redirectUrl' => $expectedOutput ]);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $actualOutput = $indodanaCommon->checkout(TestData::CHECKOUT);

    $this->assertSame(
      $expectedOutput,
      $actualOutput
    );
  }

  public function testCheckoutWithErrorsThrows()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage(IndodanaCommon::CHECKOUT_ERROR_MESSAGE);

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->checkout(Mockery::type('array'), Mockery::type('string'))
      ->andThrow(new IndodanaCommonException);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $indodanaCommon->checkout(TestData::CHECKOUT);
  }

  public function testGetCheckoutPayloadWithoutErrorsWillSucceed()
  {
    $expectedOutput = [ 'test' => 'test' ];

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getCheckoutPayload(Mockery::type('array'), Mockery::type('string'))
      ->andReturn($expectedOutput);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $actualOutput = $indodanaCommon->getCheckoutPayload(TestData::CHECKOUT);

    $this->assertSame(
      $expectedOutput,
      $actualOutput
    );
  }

  public function testGetCheckoutPayloadWithErrorsThrows()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage(IndodanaCommon::CHECKOUT_ERROR_MESSAGE);

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getCheckoutPayload(Mockery::type('array'), Mockery::type('string'))
      ->andThrow(new IndodanaCommonException);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $indodanaCommon->getCheckoutPayload(TestData::CHECKOUT);
  }

  public function testGetBaseUrlWillSucceed()
  {
    $expectedResult = 'some_base_url';

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaMock = Mockery::mock(Indodana::class);
    $indodanaMock
      ->expects()
      ->getBaseUrl()
      ->andReturn($expectedResult);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getIndodana()
      ->andReturn($indodanaMock);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $actualResult = $indodanaCommon->getBaseUrl();

    $this->assertSame(
      $expectedResult,
      $actualResult
    );
  }

  public function testGetAuthTokenWillSucceed()
  {
    $expectedResult = 'some_auth_token';

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaMock = Mockery::mock(Indodana::class);
    $indodanaMock
      ->expects()
      ->getAuthToken()
      ->andReturn($expectedResult);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->getIndodana()
      ->andReturn($indodanaMock);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $actualResult = $indodanaCommon->getAuthToken();

    $this->assertSame(
      $expectedResult,
      $actualResult
    );
  }

  public function testIsValidAuthTokenWillSucceed()
  {
    $authToken = 'some_auth_token';

    $indodanaCommon = new IndodanaCommon(TestData::INDODANA_COMMON);

    $indodanaServiceMock = Mockery::mock(IndodanaService::class);
    $indodanaServiceMock
      ->expects()
      ->isValidAuthToken($authToken)
      ->andReturn(true);

    $indodanaCommon->setIndodanaService($indodanaServiceMock);

    $this->assertTrue(
      $indodanaCommon->isValidAuthToken($authToken)
    );
  }

  public function testGetSentryDsnWithoutErrorsWillSucceed()
  {
    $expectedResult = 'someSentryDsn';

    $indodanaSentryMock = Mockery::mock(IndodanaSentry::class);
    $indodanaSentryMock
      ->expects()
      ->getSentryDsn(Mockery::type('string'))
      ->andReturn($expectedResult);

    $actualResult = IndodanaCommon::getSentryDsn('PLUGIN', $indodanaSentryMock);

    $this->assertSame(
      $expectedResult,
      $actualResult
    );
  }

  public function testGetSentryDsnWithErrorsThrows()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage(IndodanaCommon::SETUP_SENTRY_ERROR_MESSAGE);

    $indodanaSentryMock = Mockery::mock(IndodanaSentry::class);
    $indodanaSentryMock
      ->expects()
      ->getSentryDsn(Mockery::type('string'))
      ->andThrow(new IndodanaCommonException);

    IndodanaCommon::getSentryDsn('PLUGIN', $indodanaSentryMock);
  }
}
