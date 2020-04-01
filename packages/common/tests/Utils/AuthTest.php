<?php

use PHPUnit\Framework\TestCase;
use IndodanaCommon\Utils;

class AuthTest extends TestCase
{
  protected function setUp()
  {
    $this->authUtil = new Utils\Auth();
  }

  public function testGetBearerCredentialsWithEmptyStringReturnsNull()
  {
    $this->assertSame(
      null,
      $this->authUtil->getBearerCredentials('')
    );
  }

  public function testGetBearerCredentialsWithInvalidTokenReturnsNull()
  {
    $this->assertSame(
      null,
      $this->authUtil->getBearerCredentials('test')
    );
  }

  public function testGetBearerCredentialsWithNonBearerTokenReturnsNull()
  {
    $this->assertSame(
      null,
      $this->authUtil->getBearerCredentials('Basic test:test')
    );
  }

  public function testGetBearerCredentialsWithBearerTokenReturnsMatchedCredentials()
  {
    $credentials = 'test:test';

    $this->assertSame(
      $credentials,
      $this->authUtil->getBearerCredentials("Bearer ${credentials}")
    );
  }
}
