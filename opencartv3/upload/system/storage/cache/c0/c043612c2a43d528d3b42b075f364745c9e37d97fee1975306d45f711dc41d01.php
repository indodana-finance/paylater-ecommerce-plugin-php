<?php

/* default/template/checkout/guest_shipping.twig */
class __TwigTemplate_db870c33e6882d7db2eb8c2920e240f1c3e69765964cb348d438bd673af63912 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<form class=\"form-horizontal\">
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-firstname\">";
        // line 3
        echo (isset($context["entry_firstname"]) ? $context["entry_firstname"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"firstname\" value=\"";
        // line 5
        echo (isset($context["firstname"]) ? $context["firstname"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_firstname"]) ? $context["entry_firstname"] : null);
        echo "\" id=\"input-shipping-firstname\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-lastname\">";
        // line 9
        echo (isset($context["entry_lastname"]) ? $context["entry_lastname"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"lastname\" value=\"";
        // line 11
        echo (isset($context["lastname"]) ? $context["lastname"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_lastname"]) ? $context["entry_lastname"] : null);
        echo "\" id=\"input-shipping-lastname\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-company\">";
        // line 15
        echo (isset($context["entry_company"]) ? $context["entry_company"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"company\" value=\"";
        // line 17
        echo (isset($context["company"]) ? $context["company"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_company"]) ? $context["entry_company"] : null);
        echo "\" id=\"input-shipping-company\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-address-1\">";
        // line 21
        echo (isset($context["entry_address_1"]) ? $context["entry_address_1"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"address_1\" value=\"";
        // line 23
        echo (isset($context["address_1"]) ? $context["address_1"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_address_1"]) ? $context["entry_address_1"] : null);
        echo "\" id=\"input-shipping-address-1\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-address-2\">";
        // line 27
        echo (isset($context["entry_address_2"]) ? $context["entry_address_2"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"address_2\" value=\"";
        // line 29
        echo (isset($context["address_2"]) ? $context["address_2"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_address_2"]) ? $context["entry_address_2"] : null);
        echo "\" id=\"input-shipping-address-2\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-city\">";
        // line 33
        echo (isset($context["entry_city"]) ? $context["entry_city"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"city\" value=\"";
        // line 35
        echo (isset($context["city"]) ? $context["city"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_city"]) ? $context["entry_city"] : null);
        echo "\" id=\"input-shipping-city\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-postcode\">";
        // line 39
        echo (isset($context["entry_postcode"]) ? $context["entry_postcode"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"postcode\" value=\"";
        // line 41
        echo (isset($context["postcode"]) ? $context["postcode"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_postcode"]) ? $context["entry_postcode"] : null);
        echo "\" id=\"input-shipping-postcode\" class=\"form-control\" />
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-country\">";
        // line 45
        echo (isset($context["entry_country"]) ? $context["entry_country"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <select name=\"country_id\" id=\"input-shipping-country\" class=\"form-control\">
        <option value=\"\">";
        // line 48
        echo (isset($context["text_select"]) ? $context["text_select"] : null);
        echo "</option>
        
       ";
        // line 50
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["countries"]) ? $context["countries"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["country"]) {
            // line 51
            echo "        ";
            if (($this->getAttribute($context["country"], "country_id", array()) == (isset($context["country_id"]) ? $context["country_id"] : null))) {
                // line 52
                echo "        
        <option value=\"";
                // line 53
                echo $this->getAttribute($context["country"], "country_id", array());
                echo "\" selected=\"selected\">";
                echo $this->getAttribute($context["country"], "name", array());
                echo "</option>
        
        ";
            } else {
                // line 56
                echo "        
        <option value=\"";
                // line 57
                echo $this->getAttribute($context["country"], "country_id", array());
                echo "\">";
                echo $this->getAttribute($context["country"], "name", array());
                echo "</option>
        
        ";
            }
            // line 60
            echo "        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['country'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 61
        echo "      
      </select>
    </div>
  </div>
  <div class=\"form-group required\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-zone\">";
        // line 66
        echo (isset($context["entry_zone"]) ? $context["entry_zone"] : null);
        echo "</label>
    <div class=\"col-sm-10\">
      <select name=\"zone_id\" id=\"input-shipping-zone\" class=\"form-control\">
      </select>
    </div>
  </div>
  ";
        // line 72
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["custom_fields"]) ? $context["custom_fields"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["custom_field"]) {
            // line 73
            echo "  ";
            if (($this->getAttribute($context["custom_field"], "location", array()) == "address")) {
                // line 74
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "select")) {
                    // line 75
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 76
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <select name=\"custom_field[";
                    // line 78
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\">
        <option value=\"\">";
                    // line 79
                    echo (isset($context["text_select"]) ? $context["text_select"] : null);
                    echo "</option>
        
        ";
                    // line 81
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["custom_field"], "custom_field_value", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["custom_field_value"]) {
                        // line 82
                        echo "        ";
                        if (($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array") && ($this->getAttribute($context["custom_field_value"], "custom_field_value_id", array()) == $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")))) {
                            // line 83
                            echo "        
        <option value=\"";
                            // line 84
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\" selected=\"selected\">";
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</option>
        
        ";
                        } else {
                            // line 87
                            echo "        
        <option value=\"";
                            // line 88
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\">";
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</option>
        
        ";
                        }
                        // line 91
                        echo "        ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['custom_field_value'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 92
                    echo "      
      </select>
    </div>
  </div>
  ";
                }
                // line 97
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "radio")) {
                    // line 98
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\">";
                    // line 99
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <div id=\"input-shipping-custom-field";
                    // line 101
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\"> ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["custom_field"], "custom_field_value", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["custom_field_value"]) {
                        // line 102
                        echo "        <div class=\"radio\"> ";
                        if (($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array") && ($this->getAttribute($context["custom_field_value"], "custom_field_value_id", array()) == $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")))) {
                            // line 103
                            echo "          <label>
            <input type=\"radio\" name=\"custom_field[";
                            // line 104
                            echo $this->getAttribute($context["custom_field"], "location", array());
                            echo "][";
                            echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                            echo "]\" value=\"";
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\" checked=\"checked\" />
            ";
                            // line 105
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</label>
          ";
                        } else {
                            // line 107
                            echo "          <label>
            <input type=\"radio\" name=\"custom_field[";
                            // line 108
                            echo $this->getAttribute($context["custom_field"], "location", array());
                            echo "][";
                            echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                            echo "]\" value=\"";
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\" />
            ";
                            // line 109
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</label>
          ";
                        }
                        // line 110
                        echo " </div>
        ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['custom_field_value'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 111
                    echo " </div>
    </div>
  </div>
  ";
                }
                // line 115
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "checkbox")) {
                    // line 116
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\">";
                    // line 117
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <div id=\"input-shipping-custom-field";
                    // line 119
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\"> ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["custom_field"], "custom_field_value", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["custom_field_value"]) {
                        // line 120
                        echo "        <div class=\"checkbox\"> ";
                        if (($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array") && twig_in_filter($this->getAttribute($context["custom_field_value"], "custom_field_value_id", array()), $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")))) {
                            // line 121
                            echo "          <label>
            <input type=\"checkbox\" name=\"custom_field[";
                            // line 122
                            echo $this->getAttribute($context["custom_field"], "location", array());
                            echo "][";
                            echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                            echo "][]\" value=\"";
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\" checked=\"checked\" />
            ";
                            // line 123
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</label>
          ";
                        } else {
                            // line 125
                            echo "          <label>
            <input type=\"checkbox\" name=\"custom_field[";
                            // line 126
                            echo $this->getAttribute($context["custom_field"], "location", array());
                            echo "][";
                            echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                            echo "][]\" value=\"";
                            echo $this->getAttribute($context["custom_field_value"], "custom_field_value_id", array());
                            echo "\" />
            ";
                            // line 127
                            echo $this->getAttribute($context["custom_field_value"], "name", array());
                            echo "</label>
          ";
                        }
                        // line 128
                        echo " </div>
        ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['custom_field_value'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 129
                    echo " </div>
    </div>
  </div>
  ";
                }
                // line 133
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "text")) {
                    // line 134
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 135
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" name=\"custom_field[";
                    // line 137
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" value=\"";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                    } else {
                        echo $this->getAttribute($context["custom_field"], "value", array());
                    }
                    echo "\" placeholder=\"";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\" />
    </div>
  </div>
  ";
                }
                // line 141
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "textarea")) {
                    // line 142
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 143
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <textarea name=\"custom_field[";
                    // line 145
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" rows=\"5\" placeholder=\"";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\">";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                    } else {
                        echo $this->getAttribute($context["custom_field"], "value", array());
                    }
                    echo "</textarea>
    </div>
  </div>
  ";
                }
                // line 149
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "file")) {
                    // line 150
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\">";
                    // line 151
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <button type=\"button\" id=\"button-shipping-custom-field";
                    // line 153
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" data-loading-text=\"";
                    echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
                    echo "\" class=\"btn btn-default\"><i class=\"fa fa-upload\"></i> ";
                    echo (isset($context["button_upload"]) ? $context["button_upload"] : null);
                    echo "</button>
      <input type=\"hidden\" name=\"custom_field[";
                    // line 154
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" value=\"";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo "  ";
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                        echo " ";
                    }
                    echo "\" />
    </div>
  </div>
  ";
                }
                // line 158
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "date")) {
                    // line 159
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 160
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <div class=\"input-group date\">
        <input type=\"text\" name=\"custom_field[";
                    // line 163
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" value=\"";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                    } else {
                        echo $this->getAttribute($context["custom_field"], "value", array());
                    }
                    echo "\" placeholder=\"";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "\" data-date-format=\"YYYY-MM-DD\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\" />
        <span class=\"input-group-btn\">
        <button type=\"button\" class=\"btn btn-default\"><i class=\"fa fa-calendar\"></i></button>
        </span></div>
    </div>
  </div>
  ";
                }
                // line 170
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "time")) {
                    // line 171
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 172
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <div class=\"input-group time\">
        <input type=\"text\" name=\"custom_field[";
                    // line 175
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" value=\"";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                    } else {
                        echo $this->getAttribute($context["custom_field"], "value", array());
                    }
                    echo "\" placeholder=\"";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "\" data-date-format=\"HH:mm\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\" />
        <span class=\"input-group-btn\">
        <button type=\"button\" class=\"btn btn-default\"><i class=\"fa fa-calendar\"></i></button>
        </span></div>
    </div>
  </div>
  ";
                }
                // line 182
                echo "  ";
                if (($this->getAttribute($context["custom_field"], "type", array()) == "time")) {
                    // line 183
                    echo "  <div class=\"form-group";
                    if ($this->getAttribute($context["custom_field"], "required", array())) {
                        echo " required ";
                    }
                    echo " custom-field\" data-sort=\"";
                    echo $this->getAttribute($context["custom_field"], "sort_order", array());
                    echo "\">
    <label class=\"col-sm-2 control-label\" for=\"input-shipping-custom-field";
                    // line 184
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\">";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "</label>
    <div class=\"col-sm-10\">
      <div class=\"input-group datetime\">
        <input type=\"text\" name=\"custom_field[";
                    // line 187
                    echo $this->getAttribute($context["custom_field"], "location", array());
                    echo "][";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "]\" value=\"";
                    if ($this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array")) {
                        echo $this->getAttribute((isset($context["address_custom_field"]) ? $context["address_custom_field"] : null), $this->getAttribute($context["custom_field"], "custom_field_id", array()), array(), "array");
                    } else {
                        echo $this->getAttribute($context["custom_field"], "value", array());
                    }
                    echo "\" placeholder=\"";
                    echo $this->getAttribute($context["custom_field"], "name", array());
                    echo "\" data-date-format=\"YYYY-MM-DD HH:mm\" id=\"input-shipping-custom-field";
                    echo $this->getAttribute($context["custom_field"], "custom_field_id", array());
                    echo "\" class=\"form-control\" />
        <span class=\"input-group-btn\">
        <button type=\"button\" class=\"btn btn-default\"><i class=\"fa fa-calendar\"></i></button>
        </span></div>
    </div>
  </div>
  ";
                }
                // line 194
                echo "  ";
            }
            // line 195
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['custom_field'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 196
        echo "  <div class=\"buttons\">
    <div class=\"pull-right\">
      <input type=\"button\" value=\"";
        // line 198
        echo (isset($context["button_continue"]) ? $context["button_continue"] : null);
        echo "\" id=\"button-guest-shipping\" data-loading-text=\"";
        echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
        echo "\" class=\"btn btn-primary\" />
    </div>
  </div>
</form>
<script type=\"text/javascript\"><!--
// Sort the custom fields
\$('#collapse-shipping-address .form-group[data-sort]').detach().each(function() {
\tif (\$(this).attr('data-sort') >= 0 && \$(this).attr('data-sort') <= \$('#collapse-shipping-address .form-group').length-2) {
\t\t\$('#collapse-shipping-address .form-group').eq(parseInt(\$(this).attr('data-sort'))+2).before(this);
\t}

\tif (\$(this).attr('data-sort') > \$('#collapse-shipping-address .form-group').length-2) {
\t\t\$('#collapse-shipping-address .form-group:last').after(this);
\t}

\tif (\$(this).attr('data-sort') == \$('#collapse-shipping-address .form-group').length-2) {
\t\t\$('#collapse-shipping-address .form-group:last').after(this);
\t}

\tif (\$(this).attr('data-sort') < -\$('#collapse-shipping-address .form-group').length-2) {
\t\t\$('#collapse-shipping-address .form-group:first').before(this);
\t}
});
//--></script> 
<script type=\"text/javascript\"><!--
\$('#collapse-shipping-address button[id^=\\'button-shipping-custom-field\\']').on('click', function() {
\tvar node = this;

\t\$('#form-upload').remove();

\t\$('body').prepend('<form enctype=\"multipart/form-data\" id=\"form-upload\" style=\"display: none;\"><input type=\"file\" name=\"file\" /></form>');

\t\$('#form-upload input[name=\\'file\\']').trigger('click');

\tif (typeof timer != 'undefined') {
    \tclearInterval(timer);
\t}

\ttimer = setInterval(function() {
\t\tif (\$('#form-upload input[name=\\'file\\']').val() != '') {
\t\t\tclearInterval(timer);

\t\t\t\$.ajax({
\t\t\t\turl: 'index.php?route=tool/upload',
\t\t\t\ttype: 'post',
\t\t\t\tdataType: 'json',
\t\t\t\tdata: new FormData(\$('#form-upload')[0]),
\t\t\t\tcache: false,
\t\t\t\tcontentType: false,
\t\t\t\tprocessData: false,
\t\t\t\tbeforeSend: function() {
\t\t\t\t\t\$(node).button('loading');
\t\t\t\t},
\t\t\t\tcomplete: function() {
\t\t\t\t\t\$(node).button('reset');
\t\t\t\t},
\t\t\t\tsuccess: function(json) {
\t\t\t\t\t\$(node).parent().find('.text-danger').remove();

\t\t\t\t\tif (json['error']) {
\t\t\t\t\t\t\$(node).parent().find('input[name^=\\'custom_field\\']').after('<div class=\"text-danger\">' + json['error'] + '</div>');
\t\t\t\t\t}

\t\t\t\t\tif (json['success']) {
\t\t\t\t\t\talert(json['success']);

\t\t\t\t\t\t\$(node).parent().find('input[name^=\\'custom_field\\']').attr('value', json['file']);
\t\t\t\t\t}
\t\t\t\t},
\t\t\t\terror: function(xhr, ajaxOptions, thrownError) {
\t\t\t\t\talert(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
\t\t\t\t}
\t\t\t});
\t\t}
\t}, 500);
});
//--></script> 
<script type=\"text/javascript\"><!--
\$('.date').datetimepicker({
\tlanguage: '";
        // line 277
        echo (isset($context["datepicker"]) ? $context["datepicker"] : null);
        echo "',
\tpickTime: false
});

\$('.time').datetimepicker({
\tlanguage: '";
        // line 282
        echo (isset($context["datepicker"]) ? $context["datepicker"] : null);
        echo "',
\tpickDate: false
});

\$('.datetime').datetimepicker({
\tlanguage: '";
        // line 287
        echo (isset($context["datepicker"]) ? $context["datepicker"] : null);
        echo "',
\tpickDate: true,
\tpickTime: true
});
//--></script> 
<script type=\"text/javascript\"><!--
\$('#collapse-shipping-address select[name=\\'country_id\\']').on('change', function() {
\t\$.ajax({
\t\turl: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
\t\tdataType: 'json',
\t\tbeforeSend: function() {
\t\t\t\$('#collapse-shipping-address select[name=\\'country_id\\']').prop('disabled', true);
\t\t},
\t\tcomplete: function() {
\t\t\t\$('#collapse-shipping-address select[name=\\'country_id\\']').prop('disabled', false);
\t\t},
\t\tsuccess: function(json) {
\t\t\tif (json['postcode_required'] == '1') {
\t\t\t\t\$('#collapse-shipping-address input[name=\\'postcode\\']').parent().parent().addClass('required');
\t\t\t} else {
\t\t\t\t\$('#collapse-shipping-address input[name=\\'postcode\\']').parent().parent().removeClass('required');
\t\t\t}

\t\t\thtml = '<option value=\"\">";
        // line 310
        echo (isset($context["text_select"]) ? $context["text_select"] : null);
        echo "</option>';

\t\t\tif (json['zone'] && json['zone'] != '') {
\t\t\t\tfor (i = 0; i < json['zone'].length; i++) {
\t\t\t\t\thtml += '<option value=\"' + json['zone'][i]['zone_id'] + '\"';

\t\t\t\t\tif (json['zone'][i]['zone_id'] == '";
        // line 316
        echo (isset($context["zone_id"]) ? $context["zone_id"] : null);
        echo "') {
\t\t\t\t\t\thtml += ' selected=\"selected\"';
          \t\t\t}

\t\t\t\t\thtml += '>' + json['zone'][i]['name'] + '</option>';
\t\t\t\t}
\t\t\t} else {
\t\t\t\thtml += '<option value=\"0\" selected=\"selected\">";
        // line 323
        echo (isset($context["text_none"]) ? $context["text_none"] : null);
        echo "</option>';
\t\t\t}

\t\t\t\$('#collapse-shipping-address select[name=\\'zone_id\\']').html(html);
\t\t},
\t\terror: function(xhr, ajaxOptions, thrownError) {
\t\t\talert(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
\t\t}
\t});
});

\$('#collapse-shipping-address select[name=\\'country_id\\']').trigger('change');
//--></script>";
    }

    public function getTemplateName()
    {
        return "default/template/checkout/guest_shipping.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  806 => 323,  796 => 316,  787 => 310,  761 => 287,  753 => 282,  745 => 277,  661 => 198,  657 => 196,  651 => 195,  648 => 194,  626 => 187,  618 => 184,  609 => 183,  606 => 182,  584 => 175,  576 => 172,  567 => 171,  564 => 170,  542 => 163,  534 => 160,  525 => 159,  522 => 158,  507 => 154,  499 => 153,  494 => 151,  485 => 150,  482 => 149,  463 => 145,  456 => 143,  447 => 142,  444 => 141,  425 => 137,  418 => 135,  409 => 134,  406 => 133,  400 => 129,  393 => 128,  388 => 127,  380 => 126,  377 => 125,  372 => 123,  364 => 122,  361 => 121,  358 => 120,  352 => 119,  347 => 117,  338 => 116,  335 => 115,  329 => 111,  322 => 110,  317 => 109,  309 => 108,  306 => 107,  301 => 105,  293 => 104,  290 => 103,  287 => 102,  281 => 101,  276 => 99,  267 => 98,  264 => 97,  257 => 92,  251 => 91,  243 => 88,  240 => 87,  232 => 84,  229 => 83,  226 => 82,  222 => 81,  217 => 79,  209 => 78,  202 => 76,  193 => 75,  190 => 74,  187 => 73,  183 => 72,  174 => 66,  167 => 61,  161 => 60,  153 => 57,  150 => 56,  142 => 53,  139 => 52,  136 => 51,  132 => 50,  127 => 48,  121 => 45,  112 => 41,  107 => 39,  98 => 35,  93 => 33,  84 => 29,  79 => 27,  70 => 23,  65 => 21,  56 => 17,  51 => 15,  42 => 11,  37 => 9,  28 => 5,  23 => 3,  19 => 1,);
    }
}
/* <form class="form-horizontal">*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-firstname">{{ entry_firstname }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="firstname" value="{{ firstname }}" placeholder="{{ entry_firstname }}" id="input-shipping-firstname" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-lastname">{{ entry_lastname }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="lastname" value="{{ lastname }}" placeholder="{{ entry_lastname }}" id="input-shipping-lastname" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-company">{{ entry_company }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="company" value="{{ company }}" placeholder="{{ entry_company }}" id="input-shipping-company" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-address-1">{{ entry_address_1 }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="address_1" value="{{ address_1 }}" placeholder="{{ entry_address_1 }}" id="input-shipping-address-1" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-address-2">{{ entry_address_2 }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="address_2" value="{{ address_2 }}" placeholder="{{ entry_address_2 }}" id="input-shipping-address-2" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-city">{{ entry_city }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="city" value="{{ city }}" placeholder="{{ entry_city }}" id="input-shipping-city" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-postcode">{{ entry_postcode }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="postcode" value="{{ postcode }}" placeholder="{{ entry_postcode }}" id="input-shipping-postcode" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-country">{{ entry_country }}</label>*/
/*     <div class="col-sm-10">*/
/*       <select name="country_id" id="input-shipping-country" class="form-control">*/
/*         <option value="">{{ text_select }}</option>*/
/*         */
/*        {% for country in countries %}*/
/*         {% if country.country_id == country_id %}*/
/*         */
/*         <option value="{{ country.country_id }}" selected="selected">{{ country.name }}</option>*/
/*         */
/*         {% else %}*/
/*         */
/*         <option value="{{ country.country_id }}">{{ country.name }}</option>*/
/*         */
/*         {% endif %}*/
/*         {% endfor %}*/
/*       */
/*       </select>*/
/*     </div>*/
/*   </div>*/
/*   <div class="form-group required">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-zone">{{ entry_zone }}</label>*/
/*     <div class="col-sm-10">*/
/*       <select name="zone_id" id="input-shipping-zone" class="form-control">*/
/*       </select>*/
/*     </div>*/
/*   </div>*/
/*   {% for custom_field in custom_fields %}*/
/*   {% if custom_field.location == 'address' %}*/
/*   {% if custom_field.type == 'select' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <select name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control">*/
/*         <option value="">{{ text_select }}</option>*/
/*         */
/*         {% for custom_field_value in custom_field.custom_field_value %}*/
/*         {% if address_custom_field[custom_field.custom_field_id] and custom_field_value.custom_field_value_id == address_custom_field[custom_field.custom_field_id] %}*/
/*         */
/*         <option value="{{ custom_field_value.custom_field_value_id }}" selected="selected">{{ custom_field_value.name }}</option>*/
/*         */
/*         {% else %}*/
/*         */
/*         <option value="{{ custom_field_value.custom_field_value_id }}">{{ custom_field_value.name }}</option>*/
/*         */
/*         {% endif %}*/
/*         {% endfor %}*/
/*       */
/*       </select>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'radio' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <div id="input-shipping-custom-field{{ custom_field.custom_field_id }}"> {% for custom_field_value in custom_field.custom_field_value %}*/
/*         <div class="radio"> {% if address_custom_field[custom_field.custom_field_id] and custom_field_value.custom_field_value_id == address_custom_field[custom_field.custom_field_id] %}*/
/*           <label>*/
/*             <input type="radio" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{{ custom_field_value.custom_field_value_id }}" checked="checked" />*/
/*             {{ custom_field_value.name }}</label>*/
/*           {% else %}*/
/*           <label>*/
/*             <input type="radio" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{{ custom_field_value.custom_field_value_id }}" />*/
/*             {{ custom_field_value.name }}</label>*/
/*           {% endif %} </div>*/
/*         {% endfor %} </div>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'checkbox' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <div id="input-shipping-custom-field{{ custom_field.custom_field_id }}"> {% for custom_field_value in custom_field.custom_field_value %}*/
/*         <div class="checkbox"> {% if address_custom_field[custom_field.custom_field_id] and custom_field_value.custom_field_value_id in address_custom_field[custom_field.custom_field_id] %}*/
/*           <label>*/
/*             <input type="checkbox" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}][]" value="{{ custom_field_value.custom_field_value_id }}" checked="checked" />*/
/*             {{ custom_field_value.name }}</label>*/
/*           {% else %}*/
/*           <label>*/
/*             <input type="checkbox" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}][]" value="{{ custom_field_value.custom_field_value_id }}" />*/
/*             {{ custom_field_value.name }}</label>*/
/*           {% endif %} </div>*/
/*         {% endfor %} </div>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'text' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <input type="text" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{% if address_custom_field[custom_field.custom_field_id] %}{{ address_custom_field[custom_field.custom_field_id] }}{% else %}{{ custom_field.value }}{% endif %}" placeholder="{{ custom_field.name }}" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control" />*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'textarea' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <textarea name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" rows="5" placeholder="{{ custom_field.name }}" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control">{% if address_custom_field[custom_field.custom_field_id] %}{{ address_custom_field[custom_field.custom_field_id] }}{% else %}{{ custom_field.value }}{% endif %}</textarea>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'file' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <button type="button" id="button-shipping-custom-field{{ custom_field.custom_field_id }}" data-loading-text="{{ text_loading }}" class="btn btn-default"><i class="fa fa-upload"></i> {{ button_upload }}</button>*/
/*       <input type="hidden" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{% if address_custom_field[custom_field.custom_field_id] %}  {{ address_custom_field[custom_field.custom_field_id] }} {% endif %}" />*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'date' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <div class="input-group date">*/
/*         <input type="text" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{% if address_custom_field[custom_field.custom_field_id] %}{{ address_custom_field[custom_field.custom_field_id] }}{% else %}{{ custom_field.value }}{% endif %}" placeholder="{{ custom_field.name }}" data-date-format="YYYY-MM-DD" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control" />*/
/*         <span class="input-group-btn">*/
/*         <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>*/
/*         </span></div>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'time' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <div class="input-group time">*/
/*         <input type="text" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{% if address_custom_field[custom_field.custom_field_id] %}{{ address_custom_field[custom_field.custom_field_id] }}{% else %}{{ custom_field.value }}{% endif %}" placeholder="{{ custom_field.name }}" data-date-format="HH:mm" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control" />*/
/*         <span class="input-group-btn">*/
/*         <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>*/
/*         </span></div>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% if custom_field.type == 'time' %}*/
/*   <div class="form-group{% if custom_field.required %} required {% endif %} custom-field" data-sort="{{ custom_field.sort_order }}">*/
/*     <label class="col-sm-2 control-label" for="input-shipping-custom-field{{ custom_field.custom_field_id }}">{{ custom_field.name }}</label>*/
/*     <div class="col-sm-10">*/
/*       <div class="input-group datetime">*/
/*         <input type="text" name="custom_field[{{ custom_field.location }}][{{ custom_field.custom_field_id }}]" value="{% if address_custom_field[custom_field.custom_field_id] %}{{ address_custom_field[custom_field.custom_field_id] }}{% else %}{{ custom_field.value }}{% endif %}" placeholder="{{ custom_field.name }}" data-date-format="YYYY-MM-DD HH:mm" id="input-shipping-custom-field{{ custom_field.custom_field_id }}" class="form-control" />*/
/*         <span class="input-group-btn">*/
/*         <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>*/
/*         </span></div>*/
/*     </div>*/
/*   </div>*/
/*   {% endif %}*/
/*   {% endif %}*/
/*   {% endfor %}*/
/*   <div class="buttons">*/
/*     <div class="pull-right">*/
/*       <input type="button" value="{{ button_continue }}" id="button-guest-shipping" data-loading-text="{{ text_loading }}" class="btn btn-primary" />*/
/*     </div>*/
/*   </div>*/
/* </form>*/
/* <script type="text/javascript"><!--*/
/* // Sort the custom fields*/
/* $('#collapse-shipping-address .form-group[data-sort]').detach().each(function() {*/
/* 	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#collapse-shipping-address .form-group').length-2) {*/
/* 		$('#collapse-shipping-address .form-group').eq(parseInt($(this).attr('data-sort'))+2).before(this);*/
/* 	}*/
/* */
/* 	if ($(this).attr('data-sort') > $('#collapse-shipping-address .form-group').length-2) {*/
/* 		$('#collapse-shipping-address .form-group:last').after(this);*/
/* 	}*/
/* */
/* 	if ($(this).attr('data-sort') == $('#collapse-shipping-address .form-group').length-2) {*/
/* 		$('#collapse-shipping-address .form-group:last').after(this);*/
/* 	}*/
/* */
/* 	if ($(this).attr('data-sort') < -$('#collapse-shipping-address .form-group').length-2) {*/
/* 		$('#collapse-shipping-address .form-group:first').before(this);*/
/* 	}*/
/* });*/
/* //--></script> */
/* <script type="text/javascript"><!--*/
/* $('#collapse-shipping-address button[id^=\'button-shipping-custom-field\']').on('click', function() {*/
/* 	var node = this;*/
/* */
/* 	$('#form-upload').remove();*/
/* */
/* 	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');*/
/* */
/* 	$('#form-upload input[name=\'file\']').trigger('click');*/
/* */
/* 	if (typeof timer != 'undefined') {*/
/*     	clearInterval(timer);*/
/* 	}*/
/* */
/* 	timer = setInterval(function() {*/
/* 		if ($('#form-upload input[name=\'file\']').val() != '') {*/
/* 			clearInterval(timer);*/
/* */
/* 			$.ajax({*/
/* 				url: 'index.php?route=tool/upload',*/
/* 				type: 'post',*/
/* 				dataType: 'json',*/
/* 				data: new FormData($('#form-upload')[0]),*/
/* 				cache: false,*/
/* 				contentType: false,*/
/* 				processData: false,*/
/* 				beforeSend: function() {*/
/* 					$(node).button('loading');*/
/* 				},*/
/* 				complete: function() {*/
/* 					$(node).button('reset');*/
/* 				},*/
/* 				success: function(json) {*/
/* 					$(node).parent().find('.text-danger').remove();*/
/* */
/* 					if (json['error']) {*/
/* 						$(node).parent().find('input[name^=\'custom_field\']').after('<div class="text-danger">' + json['error'] + '</div>');*/
/* 					}*/
/* */
/* 					if (json['success']) {*/
/* 						alert(json['success']);*/
/* */
/* 						$(node).parent().find('input[name^=\'custom_field\']').attr('value', json['file']);*/
/* 					}*/
/* 				},*/
/* 				error: function(xhr, ajaxOptions, thrownError) {*/
/* 					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);*/
/* 				}*/
/* 			});*/
/* 		}*/
/* 	}, 500);*/
/* });*/
/* //--></script> */
/* <script type="text/javascript"><!--*/
/* $('.date').datetimepicker({*/
/* 	language: '{{ datepicker }}',*/
/* 	pickTime: false*/
/* });*/
/* */
/* $('.time').datetimepicker({*/
/* 	language: '{{ datepicker }}',*/
/* 	pickDate: false*/
/* });*/
/* */
/* $('.datetime').datetimepicker({*/
/* 	language: '{{ datepicker }}',*/
/* 	pickDate: true,*/
/* 	pickTime: true*/
/* });*/
/* //--></script> */
/* <script type="text/javascript"><!--*/
/* $('#collapse-shipping-address select[name=\'country_id\']').on('change', function() {*/
/* 	$.ajax({*/
/* 		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,*/
/* 		dataType: 'json',*/
/* 		beforeSend: function() {*/
/* 			$('#collapse-shipping-address select[name=\'country_id\']').prop('disabled', true);*/
/* 		},*/
/* 		complete: function() {*/
/* 			$('#collapse-shipping-address select[name=\'country_id\']').prop('disabled', false);*/
/* 		},*/
/* 		success: function(json) {*/
/* 			if (json['postcode_required'] == '1') {*/
/* 				$('#collapse-shipping-address input[name=\'postcode\']').parent().parent().addClass('required');*/
/* 			} else {*/
/* 				$('#collapse-shipping-address input[name=\'postcode\']').parent().parent().removeClass('required');*/
/* 			}*/
/* */
/* 			html = '<option value="">{{ text_select }}</option>';*/
/* */
/* 			if (json['zone'] && json['zone'] != '') {*/
/* 				for (i = 0; i < json['zone'].length; i++) {*/
/* 					html += '<option value="' + json['zone'][i]['zone_id'] + '"';*/
/* */
/* 					if (json['zone'][i]['zone_id'] == '{{ zone_id }}') {*/
/* 						html += ' selected="selected"';*/
/*           			}*/
/* */
/* 					html += '>' + json['zone'][i]['name'] + '</option>';*/
/* 				}*/
/* 			} else {*/
/* 				html += '<option value="0" selected="selected">{{ text_none }}</option>';*/
/* 			}*/
/* */
/* 			$('#collapse-shipping-address select[name=\'zone_id\']').html(html);*/
/* 		},*/
/* 		error: function(xhr, ajaxOptions, thrownError) {*/
/* 			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);*/
/* 		}*/
/* 	});*/
/* });*/
/* */
/* $('#collapse-shipping-address select[name=\'country_id\']').trigger('change');*/
/* //--></script>*/
