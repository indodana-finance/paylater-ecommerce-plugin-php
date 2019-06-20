<?php

/* default/template/checkout/payment_method.twig */
class __TwigTemplate_301413c87e8b575850d4bb2a3537147581213d73331e34b936379081f9216115 extends Twig_Template
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
        if ((isset($context["error_warning"]) ? $context["error_warning"] : null)) {
            // line 2
            echo "<div class=\"alert alert-warning alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo (isset($context["error_warning"]) ? $context["error_warning"] : null);
            echo "</div>
";
        }
        // line 4
        if ((isset($context["payment_methods"]) ? $context["payment_methods"] : null)) {
            // line 5
            echo "<p>";
            echo (isset($context["text_payment_method"]) ? $context["text_payment_method"] : null);
            echo "</p>
";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["payment_methods"]) ? $context["payment_methods"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["payment_method"]) {
                // line 7
                echo "<div class=\"radio\">
  <label>";
                // line 8
                if ((($this->getAttribute($context["payment_method"], "code", array()) == (isset($context["code"]) ? $context["code"] : null)) ||  !(isset($context["code"]) ? $context["code"] : null))) {
                    // line 9
                    echo "    ";
                    $context["code"] = $this->getAttribute($context["payment_method"], "code", array());
                    // line 10
                    echo "    <input type=\"radio\" name=\"payment_method\" value=\"";
                    echo $this->getAttribute($context["payment_method"], "code", array());
                    echo "\" checked=\"checked\" />
    ";
                } else {
                    // line 12
                    echo "    <input type=\"radio\" name=\"payment_method\" value=\"";
                    echo $this->getAttribute($context["payment_method"], "code", array());
                    echo "\" />
    ";
                }
                // line 14
                echo "    ";
                echo $this->getAttribute($context["payment_method"], "title", array());
                echo "
    ";
                // line 15
                if ($this->getAttribute($context["payment_method"], "terms", array())) {
                    // line 16
                    echo "    (";
                    echo $this->getAttribute($context["payment_method"], "terms", array());
                    echo ")
    ";
                }
                // line 17
                echo " </label>
</div>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['payment_method'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 21
        echo "<p><strong>";
        echo (isset($context["text_comments"]) ? $context["text_comments"] : null);
        echo "</strong></p>
<p>
  <textarea name=\"comment\" rows=\"8\" class=\"form-control\">";
        // line 23
        echo (isset($context["comment"]) ? $context["comment"] : null);
        echo "</textarea>
</p>
";
        // line 25
        if ((isset($context["text_agree"]) ? $context["text_agree"] : null)) {
            // line 26
            echo "<div class=\"buttons\">
  <div class=\"pull-right\">";
            // line 27
            echo (isset($context["text_agree"]) ? $context["text_agree"] : null);
            echo "
    ";
            // line 28
            if ((isset($context["agree"]) ? $context["agree"] : null)) {
                // line 29
                echo "    <input type=\"checkbox\" name=\"agree\" value=\"1\" checked=\"checked\" />
    ";
            } else {
                // line 31
                echo "    <input type=\"checkbox\" name=\"agree\" value=\"1\" />
    ";
            }
            // line 33
            echo "    &nbsp;
    <input type=\"button\" value=\"";
            // line 34
            echo (isset($context["button_continue"]) ? $context["button_continue"] : null);
            echo "\" id=\"button-payment-method\" data-loading-text=\"";
            echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
            echo "\" class=\"btn btn-primary\" />
  </div>
</div>
";
        } else {
            // line 38
            echo "<div class=\"buttons\">
  <div class=\"pull-right\">
    <input type=\"button\" value=\"";
            // line 40
            echo (isset($context["button_continue"]) ? $context["button_continue"] : null);
            echo "\" id=\"button-payment-method\" data-loading-text=\"";
            echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
            echo "\" class=\"btn btn-primary\" />
  </div>
</div>
";
        }
        // line 43
        echo " ";
    }

    public function getTemplateName()
    {
        return "default/template/checkout/payment_method.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  135 => 43,  126 => 40,  122 => 38,  113 => 34,  110 => 33,  106 => 31,  102 => 29,  100 => 28,  96 => 27,  93 => 26,  91 => 25,  86 => 23,  80 => 21,  71 => 17,  65 => 16,  63 => 15,  58 => 14,  52 => 12,  46 => 10,  43 => 9,  41 => 8,  38 => 7,  34 => 6,  29 => 5,  27 => 4,  21 => 2,  19 => 1,);
    }
}
/* {% if error_warning %}*/
/* <div class="alert alert-warning alert-dismissible"><i class="fa fa-exclamation-circle"></i> {{ error_warning }}</div>*/
/* {% endif %}*/
/* {% if payment_methods %}*/
/* <p>{{ text_payment_method }}</p>*/
/* {% for payment_method in payment_methods %}*/
/* <div class="radio">*/
/*   <label>{% if payment_method.code == code or not code %}*/
/*     {% set code = payment_method.code %}*/
/*     <input type="radio" name="payment_method" value="{{ payment_method.code }}" checked="checked" />*/
/*     {% else %}*/
/*     <input type="radio" name="payment_method" value="{{ payment_method.code }}" />*/
/*     {% endif %}*/
/*     {{ payment_method.title }}*/
/*     {% if payment_method.terms %}*/
/*     ({{ payment_method.terms }})*/
/*     {% endif %} </label>*/
/* </div>*/
/* {% endfor %}*/
/* {% endif %}*/
/* <p><strong>{{ text_comments }}</strong></p>*/
/* <p>*/
/*   <textarea name="comment" rows="8" class="form-control">{{ comment }}</textarea>*/
/* </p>*/
/* {% if text_agree %}*/
/* <div class="buttons">*/
/*   <div class="pull-right">{{ text_agree }}*/
/*     {% if agree %}*/
/*     <input type="checkbox" name="agree" value="1" checked="checked" />*/
/*     {% else %}*/
/*     <input type="checkbox" name="agree" value="1" />*/
/*     {% endif %}*/
/*     &nbsp;*/
/*     <input type="button" value="{{ button_continue }}" id="button-payment-method" data-loading-text="{{ text_loading }}" class="btn btn-primary" />*/
/*   </div>*/
/* </div>*/
/* {% else %}*/
/* <div class="buttons">*/
/*   <div class="pull-right">*/
/*     <input type="button" value="{{ button_continue }}" id="button-payment-method" data-loading-text="{{ text_loading }}" class="btn btn-primary" />*/
/*   </div>*/
/* </div>*/
/* {% endif %} */
