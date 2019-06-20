<?php

/* default/template/mail/order_alert.twig */
class __TwigTemplate_6e23c21634aad51a5e75aa14f1deeae987eb64d70480035fdde64c03891d785b extends Twig_Template
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
        echo (isset($context["text_received"]) ? $context["text_received"] : null);
        echo "

";
        // line 3
        echo (isset($context["text_order_id"]) ? $context["text_order_id"] : null);
        echo " ";
        echo (isset($context["order_id"]) ? $context["order_id"] : null);
        echo "
";
        // line 4
        echo (isset($context["text_date_added"]) ? $context["text_date_added"] : null);
        echo " ";
        echo (isset($context["date_added"]) ? $context["date_added"] : null);
        echo "
";
        // line 5
        echo (isset($context["text_order_status"]) ? $context["text_order_status"] : null);
        echo " ";
        echo (isset($context["order_status"]) ? $context["order_status"] : null);
        echo "

";
        // line 7
        echo (isset($context["text_product"]) ? $context["text_product"] : null);
        echo "

";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["products"]) ? $context["products"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 10
            echo $this->getAttribute($context["product"], "quantity", array());
            echo "x ";
            echo $this->getAttribute($context["product"], "name", array());
            echo " (";
            echo $this->getAttribute($context["product"], "model", array());
            echo ") ";
            echo $this->getAttribute($context["product"], "total", array());
            echo "
";
            // line 11
            if ((isset($context["option"]) ? $context["option"] : null)) {
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["product"], "option", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                    // line 13
                    echo "\t- ";
                    echo $this->getAttribute($context["option"], "name", array());
                    echo " ";
                    echo $this->getAttribute($context["option"], "value", array());
                    echo "
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['option'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        echo " 
";
        // line 17
        if ((isset($context["vouchers"]) ? $context["vouchers"] : null)) {
            // line 18
            echo "
";
            // line 19
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["vouchers"]) ? $context["vouchers"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["voucher"]) {
                // line 20
                echo "1x ";
                echo $this->getAttribute($context["voucher"], "description", array());
                echo " ";
                echo $this->getAttribute($context["voucher"], "amount", array());
                echo "
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['voucher'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo " 
";
        }
        // line 23
        echo (isset($context["text_total"]) ? $context["text_total"] : null);
        echo "

";
        // line 25
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["totals"]) ? $context["totals"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["total"]) {
            // line 26
            echo $this->getAttribute($context["total"], "title", array());
            echo ": ";
            echo $this->getAttribute($context["total"], "value", array());
            echo "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['total'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        echo " 
  
";
        // line 29
        if ((isset($context["comment"]) ? $context["comment"] : null)) {
            // line 30
            echo (isset($context["text_comment"]) ? $context["text_comment"] : null);
            echo "

";
            // line 32
            echo (isset($context["comment"]) ? $context["comment"] : null);
            echo "
";
        }
        // line 34
        echo "
";
    }

    public function getTemplateName()
    {
        return "default/template/mail/order_alert.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  146 => 34,  141 => 32,  136 => 30,  134 => 29,  130 => 27,  120 => 26,  116 => 25,  111 => 23,  107 => 21,  96 => 20,  92 => 19,  89 => 18,  87 => 17,  84 => 16,  68 => 13,  64 => 12,  62 => 11,  52 => 10,  48 => 9,  43 => 7,  36 => 5,  30 => 4,  24 => 3,  19 => 1,);
    }
}
/* {{ text_received }}*/
/* */
/* {{ text_order_id }} {{ order_id }}*/
/* {{ text_date_added }} {{ date_added }}*/
/* {{ text_order_status }} {{ order_status }}*/
/* */
/* {{ text_product }}*/
/* */
/* {% for product in products %}*/
/* {{ product.quantity }}x {{ product.name }} ({{ product.model }}) {{ product.total }}*/
/* {% if option %}*/
/* {% for option in product.option %}*/
/* 	- {{ option.name }} {{ option.value }}*/
/* {% endfor %}*/
/* {% endif %}*/
/* {% endfor %} */
/* {% if vouchers %}*/
/* */
/* {% for voucher in vouchers %}*/
/* 1x {{ voucher.description }} {{ voucher.amount }}*/
/* {% endfor %} */
/* {% endif %}*/
/* {{ text_total }}*/
/* */
/* {% for total in totals %}*/
/* {{ total.title }}: {{ total.value }}*/
/* {% endfor %} */
/*   */
/* {% if comment %}*/
/* {{ text_comment }}*/
/* */
/* {{ comment }}*/
/* {% endif %}*/
/* */
/* */
