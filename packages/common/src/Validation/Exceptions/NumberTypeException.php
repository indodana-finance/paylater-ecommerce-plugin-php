<?php

namespace IndodanaCommon\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class NumberTypeException extends ValidationException
{
  public static $defaultTemplates = [
    self::MODE_DEFAULT => array(
      self::STANDARD => '{{name}} must be of type number',
    ),
    self::MODE_NEGATIVE => array(
      self::STANDARD => '{{name}} must not be of type number',
    )
  ];
}
