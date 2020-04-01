<?php

namespace IndodanaCommon;

use Exception;
use PHPUnit\Framework\TestCase;
use phpmock\mockery\PHPMockery;
use Mockery;
use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;
use IndodanaCommon\Exceptions\IndodanaCommonException;
use IndodanaCommon\IndodanaHelper;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IndodanaHelperTest extends TestCase
{
  public function tearDown()
  {
    Mockery::close();
  }

  public function testSetIfExistsWithKeyNotExistOnSourceWillDoNothing()
  {
    $target = [ 'test' => 'test' ];
    $source = [ 'haha' => 'haha' ];
    $key = 'bye';

    IndodanaHelper::setIfExists($target, $source, $key);

    $this->assertArrayNotHasKey($key, $target);
  }

  public function testSetIfExistsWithKeyExistOnSourceWillSetKeyOnTarget()
  {
    $target = [ 'test' => 'test' ];
    $source = [ 'haha' => 'haha' ];
    $key = 'haha';

    IndodanaHelper::setIfExists($target, $source, $key);

    $this->assertArrayHasKey($key, $target);
  }

  public function testWrapIndodanaExceptionWithFunctionThatThrowsIndodanaCommonExceptionShouldExecuteDefinedErrorHandler()
  {
    $this->expectException(Exception::class);

    $indodanaLoggerMock = Mockery::mock('alias:IndodanaCommon\IndodanaLogger');
    $indodanaLoggerMock
      ->expects()
      ->error(Mockery::type('string'));

    IndodanaHelper::wrapIndodanaException(
      function() {
        throw new IndodanaCommonException();
      },
      function() {
        throw new Exception();
      }
    );
  }

  public function testWrapIndodanaExceptionWithFunctionThatThrowsIndodanaRequestExceptionShouldExecuteDefinedErrorHandler()
  {
    $this->expectException(Exception::class);

    $indodanaLoggerMock = Mockery::mock('alias:IndodanaCommon\IndodanaLogger');
    $indodanaLoggerMock
      ->expects()
      ->error(Mockery::type('string'));

    $indodanaRequestExceptionMock = Mockery::mock(IndodanaRequestException::class);
    $indodanaRequestExceptionMock
      ->expects()
      ->getErrorMessage()
      ->andReturn([]);

    IndodanaHelper::wrapIndodanaException(
      function() use ($indodanaRequestExceptionMock){
        throw $indodanaRequestExceptionMock;
      },
      function() {
        throw new Exception();
      }
    );
  }

  public function testWrapIndodanaExceptionWithFunctionThatThrowsIndodanaSdkExceptionShouldExecuteDefinedErrorHandler()
  {
    $this->expectException(Exception::class);

    $indodanaLoggerMock = Mockery::mock('alias:IndodanaCommon\IndodanaLogger');
    $indodanaLoggerMock
      ->expects()
      ->error(Mockery::type('string'));

    IndodanaHelper::wrapIndodanaException(
      function() {
        throw new IndodanaSdkException();
      },
      function() {
        throw new Exception();
      }
    );
  }

  public function testGetRequestBodyReturnDesiredOutput()
  {
    $desiredOutput = [];

    PHPMockery::mock(__NAMESPACE__, 'file_get_contents')->andReturn('');
    PHPMockery::mock(__NAMESPACE__, 'json_decode')->andReturn($desiredOutput);

    $this->assertSame(
      $desiredOutput,
      IndodanaHelper::getRequestBody()
    );
  }
}
