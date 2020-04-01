<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaSentry;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IndodanaSentryTest extends TestCase
{
  public function testGetSentryDsnWithoutResponseThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $indodanaHttpClientMock = Mockery::mock('alias:Indodana\IndodanaHttpClient');
    $indodanaHttpClientMock
      ->expects()
      ->get(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'));

    $indodanaSentry = new IndodanaSentry();

    $indodanaSentry->getSentryDsn('PLUGIN');
  }

  public function testGetSentryDsnWithoutResponseDataThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $indodanaHttpClientMock = Mockery::mock('alias:Indodana\IndodanaHttpClient');
    $indodanaHttpClientMock
      ->expects()
      ->get(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'))
      ->andReturn([]);

    $indodanaSentry = new IndodanaSentry();

    $indodanaSentry->getSentryDsn('PLUGIN');
  }

  public function testGetSentryDsnWithoutResponseDataSentryDsnThrows()
  {
    $this->expectException(IndodanaCommonException::class);

    $indodanaHttpClientMock = Mockery::mock('alias:Indodana\IndodanaHttpClient');
    $indodanaHttpClientMock
      ->expects()
      ->get(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'))
      ->andReturn([
        'data' => []
      ]);

    $indodanaSentry = new IndodanaSentry();

    $indodanaSentry->getSentryDsn('PLUGIN');
  }

  public function testGetSentryDsnWithResponseDataSentryDsnReturnSentryDsn()
  {
    $configuredSentryDsn = 'PLUGIN';

    $indodanaHttpClientMock = Mockery::mock('alias:Indodana\IndodanaHttpClient');
    $indodanaHttpClientMock
      ->expects()
      ->get(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'))
      ->andReturn([
        'data' => [
          'sentryDsn' => $configuredSentryDsn
        ]
      ]);

    $indodanaSentry = new IndodanaSentry();

    $sentryDsn = $indodanaSentry->getSentryDsn('PLUGIN');

    $this->assertSame(
      $configuredSentryDsn,
      $sentryDsn
    );
  }

}
