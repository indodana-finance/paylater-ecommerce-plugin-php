<?php

require_once Mage::getBaseDir('lib') . '/Indodana/Payment/autoload.php';

use IndodanaCommon\IndodanaConstant;

class Indodana_Payment_Model_System_Config_Source_CountryCode
{
  public function toOptionArray()
  {
    $countryCodeMapping = IndodanaConstant::getCountryCodeMapping();

    $options = [];

    foreach ( $countryCodeMapping as $key => $value ) {
      $options[] = [ 'value' => $key, 'label' => $value ];
    }

    return $options;
  }
}
