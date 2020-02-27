<?php

class Indodana_Payment_Model_System_Config_Source_Environment
{
  public function toOptionArray()
  {
    return [
      ['value' => 'sandbox', 'label' => 'Sandbox'],
      ['value' => 'production', 'label' => 'Production']
    ];
  }
}
