<?php

/* extension/payment/alipay.twig */
class __TwigTemplate_d22a3fd5faaa6ee3779ab8212d078b943d80096b6e208679799a5aed982d56de extends Twig_Template
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
        echo (isset($context["header"]) ? $context["header"] : null);
        echo (isset($context["column_left"]) ? $context["column_left"] : null);
        echo "
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"pull-right\">
        <button type=\"submit\" form=\"form-payment\" data-toggle=\"tooltip\" title=\"";
        // line 6
        echo (isset($context["button_save"]) ? $context["button_save"] : null);
        echo "\" class=\"btn btn-primary\"><i class=\"fa fa-save\"></i></button>
        <a href=\"";
        // line 7
        echo (isset($context["cancel"]) ? $context["cancel"] : null);
        echo "\" data-toggle=\"tooltip\" title=\"";
        echo (isset($context["button_cancel"]) ? $context["button_cancel"] : null);
        echo "\" class=\"btn btn-default\"><i class=\"fa fa-reply\"></i></a></div>
      <h1>";
        // line 8
        echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
        echo "</h1>
      <ul class=\"breadcrumb\">
        ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["breadcrumbs"]) ? $context["breadcrumbs"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
            // line 11
            echo "        <li><a href=\"";
            echo $this->getAttribute($context["breadcrumb"], "href", array());
            echo "\">";
            echo $this->getAttribute($context["breadcrumb"], "text", array());
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "      </ul>
    </div>
  </div>
  <div class=\"container-fluid\">
    ";
        // line 17
        if ((isset($context["error_warning"]) ? $context["error_warning"] : null)) {
            // line 18
            echo "    <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo (isset($context["error_warning"]) ? $context["error_warning"] : null);
            echo "
      <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
    </div>
    ";
        }
        // line 22
        echo "    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
        <h3 class=\"panel-title\"><i class=\"fa fa-pencil\"></i> ";
        // line 24
        echo (isset($context["text_edit"]) ? $context["text_edit"] : null);
        echo "</h3>
      </div>
      <div class=\"panel-body\">
        <form action=\"";
        // line 27
        echo (isset($context["action"]) ? $context["action"] : null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\" id=\"form-payment\" class=\"form-horizontal\">
          <div class=\"tab-content\">
            <div class=\"form-group required\">
              <label class=\"col-sm-2 control-label\" for=\"entry-app-id\">";
        // line 30
        echo (isset($context["entry_app_id"]) ? $context["entry_app_id"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <input type=\"text\" name=\"payment_alipay_app_id\" value=\"";
        // line 32
        echo (isset($context["payment_alipay_app_id"]) ? $context["payment_alipay_app_id"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_app_id"]) ? $context["entry_app_id"] : null);
        echo "\" id=\"entry-app-id\" class=\"form-control\"/>
                ";
        // line 33
        if ((isset($context["error_app_id"]) ? $context["error_app_id"] : null)) {
            // line 34
            echo "                <div class=\"text-danger\">";
            echo (isset($context["error_app_id"]) ? $context["error_app_id"] : null);
            echo "</div>
                ";
        }
        // line 36
        echo "              </div>
            </div>
            <div class=\"form-group required\">
              <label class=\"col-sm-2 control-label\" for=\"entry-merchant-private-key\">";
        // line 39
        echo (isset($context["entry_merchant_private_key"]) ? $context["entry_merchant_private_key"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <textarea name=\"payment_alipay_merchant_private_key\" rows=\"10\" placeholder=\"";
        // line 41
        echo (isset($context["entry_merchant_private_key"]) ? $context["entry_merchant_private_key"] : null);
        echo "\" id=\"entry-merchant-private-key\" class=\"form-control\">";
        echo (isset($context["payment_alipay_merchant_private_key"]) ? $context["payment_alipay_merchant_private_key"] : null);
        echo "</textarea>
                ";
        // line 42
        if ((isset($context["error_merchant_private_key"]) ? $context["error_merchant_private_key"] : null)) {
            // line 43
            echo "                  <div class=\"text-danger\">";
            echo (isset($context["error_merchant_private_key"]) ? $context["error_merchant_private_key"] : null);
            echo "</div>
                ";
        }
        // line 45
        echo "              </div>
            </div>
            <div class=\"form-group required\">
              <label class=\"col-sm-2 control-label\" for=\"entry-alipay-public-key\">";
        // line 48
        echo (isset($context["entry_alipay_public_key"]) ? $context["entry_alipay_public_key"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <textarea name=\"payment_alipay_alipay_public_key\" rows=\"5\" placeholder=\"";
        // line 50
        echo (isset($context["entry_alipay_public_key"]) ? $context["entry_alipay_public_key"] : null);
        echo "\" id=\"entry-alipay-public-key\" class=\"form-control\">";
        echo (isset($context["payment_alipay_alipay_public_key"]) ? $context["payment_alipay_alipay_public_key"] : null);
        echo "</textarea>
                ";
        // line 51
        if ((isset($context["error_alipay_public_key"]) ? $context["error_alipay_public_key"] : null)) {
            // line 52
            echo "                  <div class=\"text-danger\">";
            echo (isset($context["error_alipay_public_key"]) ? $context["error_alipay_public_key"] : null);
            echo "</div>
                ";
        }
        // line 54
        echo "              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-total\"><span data-toggle=\"tooltip\" title=\"";
        // line 57
        echo (isset($context["help_total"]) ? $context["help_total"] : null);
        echo "\">";
        echo (isset($context["entry_total"]) ? $context["entry_total"] : null);
        echo "</span></label>
              <div class=\"col-sm-10\">
                <input type=\"text\" name=\"payment_alipay_total\" value=\"";
        // line 59
        echo (isset($context["payment_alipay_total"]) ? $context["payment_alipay_total"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_total"]) ? $context["entry_total"] : null);
        echo "\" id=\"input-total\" class=\"form-control\"/>
              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-order-status\">";
        // line 63
        echo (isset($context["entry_order_status"]) ? $context["entry_order_status"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <select name=\"payment_alipay_order_status_id\" id=\"input-order-status\" class=\"form-control\">
                  ";
        // line 66
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["order_statuses"]) ? $context["order_statuses"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["order_status"]) {
            // line 67
            echo "                    ";
            if (($this->getAttribute($context["order_status"], "order_status_id", array()) == (isset($context["payment_alipay_order_status_id"]) ? $context["payment_alipay_order_status_id"] : null))) {
                // line 68
                echo "                      <option value=\"";
                echo $this->getAttribute($context["order_status"], "order_status_id", array());
                echo "\" selected=\"selected\">";
                echo $this->getAttribute($context["order_status"], "name", array());
                echo "</option>
                    ";
            } else {
                // line 70
                echo "                      <option value=\"";
                echo $this->getAttribute($context["order_status"], "order_status_id", array());
                echo "\">";
                echo $this->getAttribute($context["order_status"], "name", array());
                echo "</option>
                    ";
            }
            // line 72
            echo "                  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['order_status'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        echo "                </select>
              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-geo-zone\">";
        // line 77
        echo (isset($context["entry_geo_zone"]) ? $context["entry_geo_zone"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <select name=\"payment_alipay_geo_zone_id\" id=\"input-geo-zone\" class=\"form-control\">
                  <option value=\"0\">";
        // line 80
        echo (isset($context["text_all_zones"]) ? $context["text_all_zones"] : null);
        echo "</option>
                  ";
        // line 81
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["geo_zones"]) ? $context["geo_zones"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["geo_zone"]) {
            // line 82
            echo "                  ";
            if (($this->getAttribute($context["geo_zone"], "geo_zone_id", array()) == (isset($context["payment_alipay_geo_zone_id"]) ? $context["payment_alipay_geo_zone_id"] : null))) {
                // line 83
                echo "                  <option value=\"";
                echo $this->getAttribute($context["geo_zone"], "geo_zone_id", array());
                echo "\" selected=\"selected\">";
                echo $this->getAttribute($context["geo_zone"], "name", array());
                echo "</option>
                  ";
            } else {
                // line 85
                echo "                  <option value=\"";
                echo $this->getAttribute($context["geo_zone"], "geo_zone_id", array());
                echo "\">";
                echo $this->getAttribute($context["geo_zone"], "name", array());
                echo "</option>
                  ";
            }
            // line 87
            echo "                  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['geo_zone'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 88
        echo "                </select>
              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-test\"><span data-toggle=\"tooltip\" title=\"";
        // line 92
        echo (isset($context["help_test"]) ? $context["help_test"] : null);
        echo "\">";
        echo (isset($context["entry_test"]) ? $context["entry_test"] : null);
        echo "</span></label>
              <div class=\"col-sm-10\">
                <select name=\"payment_alipay_test\" id=\"input-test\" class=\"form-control\">
                  ";
        // line 95
        if (((isset($context["payment_alipay_test"]) ? $context["payment_alipay_test"] : null) == "sandbox")) {
            // line 96
            echo "                    <option value=\"sandbox\" selected=\"selected\">";
            echo (isset($context["text_sandbox"]) ? $context["text_sandbox"] : null);
            echo "</option>
                  ";
        } else {
            // line 98
            echo "                    <option value=\"sandbox\">";
            echo (isset($context["text_sandbox"]) ? $context["text_sandbox"] : null);
            echo "</option>
                  ";
        }
        // line 100
        echo "                  ";
        if (((isset($context["payment_alipay_test"]) ? $context["payment_alipay_test"] : null) == "live")) {
            // line 101
            echo "                    <option value=\"live\" selected=\"selected\">";
            echo (isset($context["text_live"]) ? $context["text_live"] : null);
            echo "</option>
                  ";
        } else {
            // line 103
            echo "                    <option value=\"live\">";
            echo (isset($context["text_live"]) ? $context["text_live"] : null);
            echo "</option>
                  ";
        }
        // line 105
        echo "                </select>
              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-status\">";
        // line 109
        echo (isset($context["entry_status"]) ? $context["entry_status"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <select name=\"payment_alipay_status\" id=\"input-status\" class=\"form-control\">
                  ";
        // line 112
        if ((isset($context["payment_alipay_status"]) ? $context["payment_alipay_status"] : null)) {
            // line 113
            echo "                  <option value=\"1\" selected=\"selected\">";
            echo (isset($context["text_enabled"]) ? $context["text_enabled"] : null);
            echo "</option>
                  <option value=\"0\">";
            // line 114
            echo (isset($context["text_disabled"]) ? $context["text_disabled"] : null);
            echo "</option>
                  ";
        } else {
            // line 116
            echo "                  <option value=\"1\">";
            echo (isset($context["text_enabled"]) ? $context["text_enabled"] : null);
            echo "</option>
                  <option value=\"0\" selected=\"selected\">";
            // line 117
            echo (isset($context["text_disabled"]) ? $context["text_disabled"] : null);
            echo "</option>
                  ";
        }
        // line 119
        echo "                </select>
              </div>
            </div>
            <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-sort-order\">";
        // line 123
        echo (isset($context["entry_sort_order"]) ? $context["entry_sort_order"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                <input type=\"text\" name=\"payment_alipay_sort_order\" value=\"";
        // line 125
        echo (isset($context["payment_alipay_sort_order"]) ? $context["payment_alipay_sort_order"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["entry_sort_order"]) ? $context["entry_sort_order"] : null);
        echo "\" id=\"input-sort-order\" class=\"form-control\"/>
              </div>
            </div>
          </div>
        </form>
        <div class=\"alert alert-info\">";
        // line 130
        echo (isset($context["help_alipay_setup"]) ? $context["help_alipay_setup"] : null);
        echo "</div>
      </div>
    </div>
  </div>
</div>
";
        // line 135
        echo (isset($context["footer"]) ? $context["footer"] : null);
    }

    public function getTemplateName()
    {
        return "extension/payment/alipay.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  364 => 135,  356 => 130,  346 => 125,  341 => 123,  335 => 119,  330 => 117,  325 => 116,  320 => 114,  315 => 113,  313 => 112,  307 => 109,  301 => 105,  295 => 103,  289 => 101,  286 => 100,  280 => 98,  274 => 96,  272 => 95,  264 => 92,  258 => 88,  252 => 87,  244 => 85,  236 => 83,  233 => 82,  229 => 81,  225 => 80,  219 => 77,  213 => 73,  207 => 72,  199 => 70,  191 => 68,  188 => 67,  184 => 66,  178 => 63,  169 => 59,  162 => 57,  157 => 54,  151 => 52,  149 => 51,  143 => 50,  138 => 48,  133 => 45,  127 => 43,  125 => 42,  119 => 41,  114 => 39,  109 => 36,  103 => 34,  101 => 33,  95 => 32,  90 => 30,  84 => 27,  78 => 24,  74 => 22,  66 => 18,  64 => 17,  58 => 13,  47 => 11,  43 => 10,  38 => 8,  32 => 7,  28 => 6,  19 => 1,);
    }
}
/* {{ header }}{{ column_left }}*/
/* <div id="content">*/
/*   <div class="page-header">*/
/*     <div class="container-fluid">*/
/*       <div class="pull-right">*/
/*         <button type="submit" form="form-payment" data-toggle="tooltip" title="{{ button_save }}" class="btn btn-primary"><i class="fa fa-save"></i></button>*/
/*         <a href="{{ cancel }}" data-toggle="tooltip" title="{{ button_cancel }}" class="btn btn-default"><i class="fa fa-reply"></i></a></div>*/
/*       <h1>{{ heading_title }}</h1>*/
/*       <ul class="breadcrumb">*/
/*         {% for breadcrumb in breadcrumbs %}*/
/*         <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>*/
/*         {% endfor %}*/
/*       </ul>*/
/*     </div>*/
/*   </div>*/
/*   <div class="container-fluid">*/
/*     {% if error_warning %}*/
/*     <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> {{ error_warning }}*/
/*       <button type="button" class="close" data-dismiss="alert">&times;</button>*/
/*     </div>*/
/*     {% endif %}*/
/*     <div class="panel panel-default">*/
/*       <div class="panel-heading">*/
/*         <h3 class="panel-title"><i class="fa fa-pencil"></i> {{ text_edit }}</h3>*/
/*       </div>*/
/*       <div class="panel-body">*/
/*         <form action="{{ action }}" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">*/
/*           <div class="tab-content">*/
/*             <div class="form-group required">*/
/*               <label class="col-sm-2 control-label" for="entry-app-id">{{ entry_app_id }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <input type="text" name="payment_alipay_app_id" value="{{ payment_alipay_app_id }}" placeholder="{{ entry_app_id }}" id="entry-app-id" class="form-control"/>*/
/*                 {% if error_app_id %}*/
/*                 <div class="text-danger">{{ error_app_id }}</div>*/
/*                 {% endif %}*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group required">*/
/*               <label class="col-sm-2 control-label" for="entry-merchant-private-key">{{ entry_merchant_private_key }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <textarea name="payment_alipay_merchant_private_key" rows="10" placeholder="{{ entry_merchant_private_key }}" id="entry-merchant-private-key" class="form-control">{{ payment_alipay_merchant_private_key }}</textarea>*/
/*                 {% if error_merchant_private_key %}*/
/*                   <div class="text-danger">{{ error_merchant_private_key }}</div>*/
/*                 {% endif %}*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group required">*/
/*               <label class="col-sm-2 control-label" for="entry-alipay-public-key">{{ entry_alipay_public_key }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <textarea name="payment_alipay_alipay_public_key" rows="5" placeholder="{{ entry_alipay_public_key }}" id="entry-alipay-public-key" class="form-control">{{ payment_alipay_alipay_public_key }}</textarea>*/
/*                 {% if error_alipay_public_key %}*/
/*                   <div class="text-danger">{{ error_alipay_public_key }}</div>*/
/*                 {% endif %}*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="{{ help_total }}">{{ entry_total }}</span></label>*/
/*               <div class="col-sm-10">*/
/*                 <input type="text" name="payment_alipay_total" value="{{ payment_alipay_total }}" placeholder="{{ entry_total }}" id="input-total" class="form-control"/>*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-order-status">{{ entry_order_status }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <select name="payment_alipay_order_status_id" id="input-order-status" class="form-control">*/
/*                   {% for order_status in order_statuses %}*/
/*                     {% if order_status.order_status_id == payment_alipay_order_status_id %}*/
/*                       <option value="{{ order_status.order_status_id }}" selected="selected">{{ order_status.name }}</option>*/
/*                     {% else %}*/
/*                       <option value="{{ order_status.order_status_id }}">{{ order_status.name }}</option>*/
/*                     {% endif %}*/
/*                   {% endfor %}*/
/*                 </select>*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-geo-zone">{{ entry_geo_zone }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <select name="payment_alipay_geo_zone_id" id="input-geo-zone" class="form-control">*/
/*                   <option value="0">{{ text_all_zones }}</option>*/
/*                   {% for geo_zone in geo_zones %}*/
/*                   {% if geo_zone.geo_zone_id == payment_alipay_geo_zone_id %}*/
/*                   <option value="{{ geo_zone.geo_zone_id }}" selected="selected">{{ geo_zone.name }}</option>*/
/*                   {% else %}*/
/*                   <option value="{{ geo_zone.geo_zone_id }}">{{ geo_zone.name }}</option>*/
/*                   {% endif %}*/
/*                   {% endfor %}*/
/*                 </select>*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-test"><span data-toggle="tooltip" title="{{ help_test }}">{{ entry_test }}</span></label>*/
/*               <div class="col-sm-10">*/
/*                 <select name="payment_alipay_test" id="input-test" class="form-control">*/
/*                   {% if payment_alipay_test == 'sandbox' %}*/
/*                     <option value="sandbox" selected="selected">{{ text_sandbox }}</option>*/
/*                   {% else %}*/
/*                     <option value="sandbox">{{ text_sandbox }}</option>*/
/*                   {% endif %}*/
/*                   {% if payment_alipay_test == 'live' %}*/
/*                     <option value="live" selected="selected">{{ text_live }}</option>*/
/*                   {% else %}*/
/*                     <option value="live">{{ text_live }}</option>*/
/*                   {% endif %}*/
/*                 </select>*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-status">{{ entry_status }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <select name="payment_alipay_status" id="input-status" class="form-control">*/
/*                   {% if payment_alipay_status %}*/
/*                   <option value="1" selected="selected">{{ text_enabled }}</option>*/
/*                   <option value="0">{{ text_disabled }}</option>*/
/*                   {% else %}*/
/*                   <option value="1">{{ text_enabled }}</option>*/
/*                   <option value="0" selected="selected">{{ text_disabled }}</option>*/
/*                   {% endif %}*/
/*                 </select>*/
/*               </div>*/
/*             </div>*/
/*             <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-sort-order">{{ entry_sort_order }}</label>*/
/*               <div class="col-sm-10">*/
/*                 <input type="text" name="payment_alipay_sort_order" value="{{ payment_alipay_sort_order }}" placeholder="{{ entry_sort_order }}" id="input-sort-order" class="form-control"/>*/
/*               </div>*/
/*             </div>*/
/*           </div>*/
/*         </form>*/
/*         <div class="alert alert-info">{{ help_alipay_setup }}</div>*/
/*       </div>*/
/*     </div>*/
/*   </div>*/
/* </div>*/
/* {{ footer }}*/
