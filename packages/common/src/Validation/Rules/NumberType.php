<?php

namespace IndodanaCommon\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

final class NumberType extends AbstractRule
{
    public function validate($input)
    {
      return is_int($input) || is_float($input);
    }
}
