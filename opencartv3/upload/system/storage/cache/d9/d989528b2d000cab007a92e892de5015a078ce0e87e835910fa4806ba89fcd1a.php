<?php

/* default/template/checkout/shipping_method.twig */
class __TwigTemplate_aee9e0b8a086a468020bfcccc5ad454f5b8a3d2ffa7326cbf69288951bca3279 extends Twig_Template
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
        if ((isset($context["shipping_methods"]) ? $context["shipping_methods"] : null)) {
            // line 5
            echo "<p>";
            echo (isset($context["text_shipping_method"]) ? $context["text_shipping_method"] : null);
            echo "</p>
";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["shipping_methods"]) ? $context["shipping_methods"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["shipping_method"]) {
                // line 7
                echo "<p><strong>";
                echo $this->getAttribute($context["shipping_method"], "title", array());
                echo "</strong></p>
";
                // line 8
                if ( !$this->getAttribute($context["shipping_method"], "error", array())) {
                    // line 9
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["shipping_method"], "quote", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["quote"]) {
                        // line 10
                        echo "<div class=\"radio\">
  <label> ";
                        // line 11
                        if ((($this->getAttribute($context["quote"], "code", array()) == (isset($context["code"]) ? $context["code"] : null)) ||  !(isset($context["code"]) ? $context["code"] : null))) {
                            // line 12
                            echo "    ";
                            $context["code"] = $this->getAttribute($context["quote"], "code", array());
                            // line 13
                            echo "    <input type=\"radio\" name=\"shipping_method\" value=\"";
                            echo $this->getAttribute($context["quote"], "code", array());
                            echo "\" checked=\"checked\" />
    ";
                        } else {
                            // line 15
                            echo "    <input type=\"radio\" name=\"shipping_method\" value=\"";
                            echo $this->getAttribute($context["quote"], "code", array());
                            echo "\" />
    ";
                        }
                        // line 17
                        echo "    ";
                        echo $this->getAttribute($context["quote"], "title", array());
                        echo " - ";
                        echo $this->getAttribute($context["quote"], "text", array());
                        echo "</label>
</div>
";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['quote'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                } else {
                    // line 21
                    echo "<div class=\"alert alert-danger alert-dismissible\">";
                    echo $this->getAttribute($context["shipping_method"], "error", array());
                    echo "</div>
";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['shipping_method'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 25
        echo "<p><strong>";
        echo (isset($context["text_comments"]) ? $context["text_comments"] : null);
        echo "</strong></p>
<p>
  <textarea name=\"comment\" rows=\"8\" class=\"form-control\">";
        // line 27
        echo (isset($context["comment"]) ? $context["comment"] : null);
        echo "</textarea>
</p>
<div class=\"buttons\">
  <div class=\"pull-right\">
    <input type=\"button\" value=\"";
        // line 31
        echo (isset($context["button_continue"]) ? $context["button_continue"] : null);
        echo "\" id=\"button-shipping-method\" data-loading-text=\"";
        echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
        echo "\" class=\"btn btn-primary\" />
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "default/template/checkout/shipping_method.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 31,  99 => 27,  93 => 25,  82 => 21,  69 => 17,  63 => 15,  57 => 13,  54 => 12,  52 => 11,  49 => 10,  45 => 9,  43 => 8,  38 => 7,  34 => 6,  29 => 5,  27 => 4,  21 => 2,  19 => 1,);
    }
}
/* {% if error_warning %}*/
/* <div class="alert alert-warning alert-dismissible"><i class="fa fa-exclamation-circle"></i> {{ error_warning }}</div>*/
/* {% endif %}*/
/* {% if shipping_methods %}*/
/* <p>{{ text_shipping_method }}</p>*/
/* {% for shipping_method in shipping_methods %}*/
/* <p><strong>{{ shipping_method.title }}</strong></p>*/
/* {% if not shipping_method.error %}*/
/* {% for quote in shipping_method.quote %}*/
/* <div class="radio">*/
/*   <label> {% if quote.code == code or not code %}*/
/*     {% set code = quote.code %}*/
/*     <input type="radio" name="shipping_method" value="{{ quote.code }}" checked="checked" />*/
/*     {% else %}*/
/*     <input type="radio" name="shipping_method" value="{{ quote.code }}" />*/
/*     {% endif %}*/
/*     {{ quote.title }} - {{ quote.text }}</label>*/
/* </div>*/
/* {% endfor %}*/
/* {% else %}*/
/* <div class="alert alert-danger alert-dismissible">{{ shipping_method.error }}</div>*/
/* {% endif %}*/
/* {% endfor %}*/
/* {% endif %}*/
/* <p><strong>{{ text_comments }}</strong></p>*/
/* <p>*/
/*   <textarea name="comment" rows="8" class="form-control">{{ comment }}</textarea>*/
/* </p>*/
/* <div class="buttons">*/
/*   <div class="pull-right">*/
/*     <input type="button" value="{{ button_continue }}" id="button-shipping-method" data-loading-text="{{ text_loading }}" class="btn btn-primary" />*/
/*   </div>*/
/* </div>*/
/* */
