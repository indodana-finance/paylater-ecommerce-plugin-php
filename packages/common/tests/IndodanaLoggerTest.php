<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\IndodanaLogger;

class IndodanaLoggerTest extends TestCase
{
  public function testInfoWithoutConfiguredLogDirConstantThrows()
  {
    $this->expectException(Exception::class);

    IndodanaLogger::info('test');
  }

  public function testWarningWithoutConfiguredLogDirConstantThrows()
  {
    $this->expectException(Exception::class);

    IndodanaLogger::warning('test');
  }

  public function testErrorWithoutConfiguredLogDirConstantThrows()
  {
    $this->expectException(Exception::class);

    IndodanaLogger::error('test');
  }
}
