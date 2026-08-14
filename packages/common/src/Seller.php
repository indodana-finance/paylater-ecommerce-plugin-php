<?php

namespace IndodanaCommon;

use Indodana\Utils\Validator\Validator;
use IndodanaCommon\Exceptions\IndodanaCommonException;

class Seller
{
  private $id;
  private $payload;

  public function __construct($input = [])
  {
    $validationResult = Validator::create($input)
      ->key('url', Validator::required());

    if (!$validationResult->isSuccess()) {
      throw new IndodanaCommonException($validationResult->printErrorMessages());
    }

    $this->id = md5($input['url']);

    $this->payload = array_merge($input, [
      'id' => $this->id
    ]);
  }

  public function getId()
  {
    return $this->id;
  }

  public function getPayload()
  {
    return $this->payload;
  }
}
