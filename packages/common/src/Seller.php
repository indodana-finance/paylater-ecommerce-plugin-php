<?php

namespace IndodanaCommon;

use Respect\Validation\Validator;
use Indodana\RespectValidation\RespectValidationHelper;
use IndodanaCommon\Exceptions\IndodanaCommonException;

class Seller
{
  private $id;
  private $payload;

  public function __construct($input = [])
  {
    $validator = Validator::create()
      ->key('url', Validator::stringType()->notEmpty());

    $validationResult = RespectValidationHelper::validate($validator, $input);

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
