<?php

/* NelmioSolariumBundle:DataCollector:solarium.html.twig */
class __TwigTemplate_d67039e929324d7f9b08cd178dcd127d extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return $this->env->resolveTemplate((($this->getAttribute($this->getAttribute($this->getContext($context, "app"), "request"), "isXmlHttpRequest")) ? ("WebProfilerBundle:Profiler:ajax_layout.html.twig") : ("WebProfilerBundle:Profiler:layout.html.twig")));
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        ob_start();
        // line 5
        echo "        <img width=\"28\" height=\"28\" alt=\"Solr\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/nelmiosolarium/images/profiler/solr28.png"), "html", null, true);
        echo "\" />
        <span class=\"sf-toolbar-status";
        // line 6
        if ((50 < $this->getAttribute($this->getContext($context, "collector"), "querycount"))) {
            echo " sf-toolbar-status-yellow";
        }
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "collector"), "querycount"), "html", null, true);
        echo "</span>
        ";
        // line 7
        if (($this->getAttribute($this->getContext($context, "collector"), "querycount") > 0)) {
            // line 8
            echo "            <span class=\"sf-toolbar-info-piece-additional-detail\">in ";
            echo twig_escape_filter($this->env, sprintf("%0.2f", ($this->getAttribute($this->getContext($context, "collector"), "totaltime") * 1000)), "html", null, true);
            echo " ms</span>
        ";
        }
        // line 10
        echo "    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 11
        echo "    ";
        ob_start();
        // line 12
        echo "        <div class=\"sf-toolbar-info-piece\">
            <b>Solr Queries</b>
            <span>";
        // line 14
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "collector"), "querycount"), "html", null, true);
        echo "</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Query time</b>
            <span>";
        // line 18
        echo twig_escape_filter($this->env, sprintf("%0.2f", ($this->getAttribute($this->getContext($context, "collector"), "totaltime") * 1000)), "html", null, true);
        echo " ms</span>
        </div>
    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 21
        echo "    ";
        $this->env->loadTemplate("WebProfilerBundle:Profiler:toolbar_item.html.twig")->display(array_merge($context, array("link" => $this->getContext($context, "profiler_url"))));
    }

    // line 24
    public function block_menu($context, array $blocks = array())
    {
        // line 25
        echo "<span class=\"label\">
    <span class=\"icon\"><img src=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/nelmiosolarium/images/profiler/solr.png"), "html", null, true);
        echo "\" alt=\"Solarium\" /></span>
    <strong>Solr</strong>
    <span class=\"count\">
        <span>";
        // line 29
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "collector"), "queryCount"), "html", null, true);
        echo "</span>
        <span>";
        // line 30
        echo twig_escape_filter($this->env, sprintf("%0.0f", $this->getAttribute($this->getContext($context, "collector"), "totaltime")), "html", null, true);
        echo " ms</span>
    </span>
</span>
";
    }

    // line 35
    public function block_panel($context, array $blocks = array())
    {
        // line 36
        echo "    ";
        if (twig_test_empty($this->getAttribute($this->getContext($context, "collector"), "queries"))) {
            // line 37
            echo "        <p>
            <em>No queries.</em>
        </p>
    ";
        } else {
            // line 41
            echo "        <ul class=\"alt\">
        ";
            // line 42
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "collector"), "queries"));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["i"] => $context["query"]) {
                // line 43
                echo "            <li class=\"";
                echo twig_escape_filter($this->env, twig_cycle(array(0 => "odd", 1 => "even"), $this->getContext($context, "i")), "html", null, true);
                echo "\">
                <h2>Request ";
                // line 44
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "loop"), "index"), "html", null, true);
                echo " (<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "query"), "base_uri"), "html", null, true);
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, "query"), "request"), "uri"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "query"), "base_uri"), "html", null, true);
                echo "</a>)</a></h2>
                <div>
                    <h3>Params</h3>
                    ";
                // line 54
                echo "                    <table style=\"width:45%\">
                        <thead>
                            <tr>
                                <th scope=\"col\">Key</th>
                                <th scope=\"col\">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                    ";
                // line 62
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getContext($context, "query"), "request"), "params"));
                foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                    // line 63
                    echo "                            <tr>
                                <td><b>";
                    // line 64
                    echo twig_escape_filter($this->env, $this->getContext($context, "key"), "html", null, true);
                    echo "</b></td>
                                ";
                    // line 65
                    if (twig_get_array_keys_filter($this->getContext($context, "value"))) {
                        // line 66
                        echo "                                <td>";
                        echo twig_join_filter($this->getContext($context, "value"), "<br />");
                        echo "</td>
                                ";
                    } else {
                        // line 68
                        echo "                                <td>";
                        echo twig_escape_filter($this->env, $this->getContext($context, "value"), "html", null, true);
                        echo "</td>
                                ";
                    }
                    // line 70
                    echo "                            </tr>
                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 72
                echo "                        </tbody>
                    </table>
                    
                    <h3>Response</h3>
                    <code>
                        HTTP-Result: ";
                // line 77
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, "query"), "response"), "statuscode"), "html", null, true);
                echo " (";
                echo twig_escape_filter($this->env, sprintf("%0.4f", $this->getAttribute($this->getContext($context, "query"), "duration")), "html", null, true);
                echo " ms)<br/>
                        ";
                // line 78
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, "query"), "response"), "body"), "html", null, true);
                echo "
                    </code>
                </div>
            </li>
        ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['i'], $context['query'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 83
            echo "        </ul>
    ";
        }
    }

    public function getTemplateName()
    {
        return "NelmioSolariumBundle:DataCollector:solarium.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  227 => 83,  208 => 78,  195 => 72,  97 => 30,  63 => 14,  405 => 145,  399 => 144,  394 => 141,  382 => 136,  378 => 134,  369 => 132,  365 => 131,  362 => 130,  348 => 125,  334 => 115,  332 => 114,  327 => 113,  247 => 84,  239 => 82,  237 => 81,  228 => 78,  221 => 75,  211 => 71,  177 => 66,  166 => 60,  144 => 58,  140 => 56,  139 => 51,  112 => 42,  76 => 21,  155 => 56,  134 => 44,  131 => 43,  83 => 25,  43 => 8,  55 => 11,  32 => 5,  29 => 4,  788 => 469,  785 => 468,  774 => 466,  770 => 465,  766 => 463,  753 => 462,  727 => 457,  724 => 456,  705 => 454,  688 => 453,  684 => 451,  680 => 450,  676 => 449,  672 => 448,  668 => 447,  664 => 446,  661 => 445,  659 => 444,  642 => 443,  631 => 442,  616 => 437,  611 => 435,  607 => 434,  604 => 433,  602 => 432,  588 => 431,  556 => 401,  538 => 398,  521 => 397,  518 => 396,  516 => 395,  511 => 393,  506 => 391,  179 => 87,  171 => 85,  164 => 82,  159 => 80,  154 => 77,  148 => 75,  142 => 44,  124 => 45,  110 => 52,  107 => 41,  26 => 3,  203 => 71,  176 => 66,  174 => 65,  168 => 61,  158 => 59,  130 => 47,  100 => 30,  88 => 41,  79 => 22,  202 => 77,  189 => 70,  183 => 68,  165 => 64,  162 => 63,  151 => 54,  145 => 55,  136 => 45,  132 => 54,  125 => 52,  120 => 42,  93 => 29,  89 => 35,  85 => 40,  82 => 28,  47 => 8,  25 => 3,  75 => 24,  69 => 17,  66 => 30,  60 => 27,  56 => 11,  54 => 13,  42 => 10,  386 => 138,  383 => 159,  377 => 158,  375 => 157,  368 => 156,  364 => 155,  360 => 129,  358 => 152,  355 => 127,  352 => 126,  350 => 149,  342 => 147,  340 => 146,  337 => 145,  328 => 140,  325 => 139,  318 => 109,  312 => 105,  309 => 104,  306 => 103,  304 => 102,  299 => 99,  290 => 94,  287 => 93,  285 => 92,  280 => 89,  278 => 114,  273 => 111,  271 => 110,  266 => 88,  262 => 86,  256 => 103,  252 => 101,  245 => 97,  238 => 93,  232 => 79,  229 => 88,  224 => 86,  219 => 83,  213 => 79,  210 => 78,  207 => 73,  205 => 95,  200 => 69,  194 => 67,  191 => 68,  188 => 70,  186 => 67,  181 => 63,  175 => 59,  172 => 67,  169 => 62,  167 => 63,  160 => 57,  141 => 48,  128 => 42,  105 => 35,  101 => 37,  95 => 23,  86 => 20,  80 => 19,  77 => 35,  74 => 34,  71 => 19,  65 => 15,  59 => 12,  45 => 7,  34 => 5,  68 => 20,  61 => 16,  44 => 7,  37 => 6,  20 => 1,  161 => 59,  153 => 54,  150 => 49,  147 => 48,  143 => 57,  137 => 43,  121 => 35,  118 => 50,  113 => 44,  109 => 37,  106 => 31,  104 => 36,  99 => 32,  96 => 31,  94 => 31,  90 => 33,  78 => 32,  72 => 21,  62 => 14,  53 => 10,  50 => 14,  48 => 10,  41 => 9,  39 => 8,  35 => 8,  30 => 4,  27 => 3,  354 => 163,  345 => 160,  341 => 159,  338 => 117,  333 => 157,  331 => 141,  323 => 112,  321 => 149,  314 => 145,  307 => 141,  300 => 137,  293 => 95,  286 => 129,  279 => 125,  272 => 121,  257 => 109,  250 => 138,  243 => 96,  236 => 97,  226 => 87,  223 => 88,  215 => 72,  212 => 82,  209 => 81,  204 => 78,  201 => 77,  196 => 69,  190 => 72,  182 => 68,  180 => 64,  170 => 64,  163 => 62,  156 => 56,  152 => 48,  149 => 53,  146 => 74,  138 => 42,  133 => 47,  129 => 51,  126 => 50,  123 => 44,  117 => 41,  114 => 35,  111 => 37,  108 => 36,  102 => 35,  98 => 24,  91 => 28,  87 => 26,  84 => 25,  81 => 24,  73 => 23,  70 => 18,  67 => 20,  64 => 20,  58 => 13,  52 => 10,  49 => 10,  46 => 8,  40 => 7,  36 => 6,  33 => 5,  31 => 4,  28 => 3,);
    }
}
