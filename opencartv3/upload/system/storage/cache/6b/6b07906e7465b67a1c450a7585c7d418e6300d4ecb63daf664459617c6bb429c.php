<?php

/* default/template/checkout/confirm.twig */
class __TwigTemplate_a32dc765837634fd8340f5bf8292d0efe99555a47c22dd154b0b5f3955bf05f4 extends Twig_Template
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
        if ( !(isset($context["redirect"]) ? $context["redirect"] : null)) {
            // line 2
            echo "<div class=\"table-responsive\">
  <table class=\"table table-bordered table-hover\">
    <thead>
      <tr>
        <td class=\"text-left\">";
            // line 6
            echo (isset($context["column_name"]) ? $context["column_name"] : null);
            echo "</td>
        <td class=\"text-left\">";
            // line 7
            echo (isset($context["column_model"]) ? $context["column_model"] : null);
            echo "</td>
        <td class=\"text-right\">";
            // line 8
            echo (isset($context["column_quantity"]) ? $context["column_quantity"] : null);
            echo "</td>
        <td class=\"text-right\">";
            // line 9
            echo (isset($context["column_price"]) ? $context["column_price"] : null);
            echo "</td>
        <td class=\"text-right\">";
            // line 10
            echo (isset($context["column_total"]) ? $context["column_total"] : null);
            echo "</td>
      </tr>
    </thead>
    <tbody>
    
    ";
            // line 15
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["products"]) ? $context["products"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 16
                echo "    <tr>
      <td class=\"text-left\"><a href=\"";
                // line 17
                echo $this->getAttribute($context["product"], "href", array());
                echo "\">";
                echo $this->getAttribute($context["product"], "name", array());
                echo "</a> ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["product"], "option", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                    echo " <br />
        &nbsp;<small> - ";
                    // line 18
                    echo $this->getAttribute($context["option"], "name", array());
                    echo ": ";
                    echo $this->getAttribute($context["option"], "value", array());
                    echo "</small> ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['option'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 19
                echo "        ";
                if ($this->getAttribute($context["product"], "recurring", array())) {
                    echo " <br />
        <span class=\"label label-info\">";
                    // line 20
                    echo (isset($context["text_recurring_item"]) ? $context["text_recurring_item"] : null);
                    echo "</span> <small>";
                    echo $this->getAttribute($context["product"], "recurring", array());
                    echo "</small> ";
                }
                echo "</td>
      <td class=\"text-left\">";
                // line 21
                echo $this->getAttribute($context["product"], "model", array());
                echo "</td>
      <td class=\"text-right\">";
                // line 22
                echo $this->getAttribute($context["product"], "quantity", array());
                echo "</td>
      <td class=\"text-right\">";
                // line 23
                echo $this->getAttribute($context["product"], "price", array());
                echo "</td>
      <td class=\"text-right\">";
                // line 24
                echo $this->getAttribute($context["product"], "total", array());
                echo "</td>
    </tr>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 27
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["vouchers"]) ? $context["vouchers"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["voucher"]) {
                // line 28
                echo "    <tr>
      <td class=\"text-left\">";
                // line 29
                echo $this->getAttribute($context["voucher"], "description", array());
                echo "</td>
      <td class=\"text-left\"></td>
      <td class=\"text-right\">1</td>
      <td class=\"text-right\">";
                // line 32
                echo $this->getAttribute($context["voucher"], "amount", array());
                echo "</td>
      <td class=\"text-right\">";
                // line 33
                echo $this->getAttribute($context["voucher"], "amount", array());
                echo "</td>
    </tr>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['voucher'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 36
            echo "      </tbody>
    
    <tfoot>
    
    ";
            // line 40
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["totals"]) ? $context["totals"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["total"]) {
                // line 41
                echo "    <tr>
      <td colspan=\"4\" class=\"text-right\"><strong>";
                // line 42
                echo $this->getAttribute($context["total"], "title", array());
                echo ":</strong></td>
      <td class=\"text-right\">";
                // line 43
                echo $this->getAttribute($context["total"], "text", array());
                echo "</td>
    </tr>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['total'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 46
            echo "      </tfoot>
    
  </table>
</div>
";
            // line 50
            echo (isset($context["payment"]) ? $context["payment"] : null);
            echo "
";
        } else {
            // line 51
            echo " 
<script type=\"text/javascript\"><!--
location = '";
            // line 53
            echo (isset($context["redirect"]) ? $context["redirect"] : null);
            echo "';
//--></script> 
";
        }
        // line 55
        echo " ";
    }

    public function getTemplateName()
    {
        return "default/template/checkout/confirm.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  185 => 55,  179 => 53,  175 => 51,  170 => 50,  164 => 46,  155 => 43,  151 => 42,  148 => 41,  144 => 40,  138 => 36,  129 => 33,  125 => 32,  119 => 29,  116 => 28,  111 => 27,  102 => 24,  98 => 23,  94 => 22,  90 => 21,  82 => 20,  77 => 19,  68 => 18,  58 => 17,  55 => 16,  51 => 15,  43 => 10,  39 => 9,  35 => 8,  31 => 7,  27 => 6,  21 => 2,  19 => 1,);
    }
}
/* {% if not redirect %}*/
/* <div class="table-responsive">*/
/*   <table class="table table-bordered table-hover">*/
/*     <thead>*/
/*       <tr>*/
/*         <td class="text-left">{{ column_name }}</td>*/
/*         <td class="text-left">{{ column_model }}</td>*/
/*         <td class="text-right">{{ column_quantity }}</td>*/
/*         <td class="text-right">{{ column_price }}</td>*/
/*         <td class="text-right">{{ column_total }}</td>*/
/*       </tr>*/
/*     </thead>*/
/*     <tbody>*/
/*     */
/*     {% for product in products %}*/
/*     <tr>*/
/*       <td class="text-left"><a href="{{ product.href }}">{{ product.name }}</a> {% for option in product.option %} <br />*/
/*         &nbsp;<small> - {{ option.name }}: {{ option.value }}</small> {% endfor %}*/
/*         {% if product.recurring %} <br />*/
/*         <span class="label label-info">{{ text_recurring_item }}</span> <small>{{ product.recurring }}</small> {% endif %}</td>*/
/*       <td class="text-left">{{ product.model }}</td>*/
/*       <td class="text-right">{{ product.quantity }}</td>*/
/*       <td class="text-right">{{ product.price }}</td>*/
/*       <td class="text-right">{{ product.total }}</td>*/
/*     </tr>*/
/*     {% endfor %}*/
/*     {% for voucher in vouchers %}*/
/*     <tr>*/
/*       <td class="text-left">{{ voucher.description }}</td>*/
/*       <td class="text-left"></td>*/
/*       <td class="text-right">1</td>*/
/*       <td class="text-right">{{ voucher.amount }}</td>*/
/*       <td class="text-right">{{ voucher.amount }}</td>*/
/*     </tr>*/
/*     {% endfor %}*/
/*       </tbody>*/
/*     */
/*     <tfoot>*/
/*     */
/*     {% for total in totals %}*/
/*     <tr>*/
/*       <td colspan="4" class="text-right"><strong>{{ total.title }}:</strong></td>*/
/*       <td class="text-right">{{ total.text }}</td>*/
/*     </tr>*/
/*     {% endfor %}*/
/*       </tfoot>*/
/*     */
/*   </table>*/
/* </div>*/
/* {{ payment }}*/
/* {% else %} */
/* <script type="text/javascript"><!--*/
/* location = '{{ redirect }}';*/
/* //--></script> */
/* {% endif %} */
