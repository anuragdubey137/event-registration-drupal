<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* core/themes/claro/templates/details.html.twig */
class __TwigTemplate_fc062fb42266b52811bb330170a3c446 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 23
        $context["classes"] = ["claro-details", (((($tmp =         // line 25
($context["accordion"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details--accordion") : ("")), (((($tmp =         // line 26
($context["accordion_item"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details--accordion-item") : ("")), (((($tmp = (($_v0 =         // line 27
($context["element"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0["#module_package_listing"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#module_package_listing", [], "array", false, false, true, 27))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details--package-listing") : (""))];
        // line 31
        $context["content_wrapper_classes"] = ["claro-details__wrapper", "details-wrapper", (((($tmp =         // line 34
($context["accordion"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__wrapper--accordion") : ("")), (((($tmp =         // line 35
($context["accordion_item"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__wrapper--accordion-item") : ("")), (((($tmp = (($_v1 =         // line 36
($context["element"] ?? null)) && is_array($_v1) || $_v1 instanceof ArrayAccess && in_array($_v1::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v1["#module_package_listing"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#module_package_listing", [], "array", false, false, true, 36))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__wrapper--package-listing") : (""))];
        // line 40
        $context["inner_wrapper_classes"] = ["claro-details__content", (((($tmp =         // line 42
($context["accordion"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__content--accordion") : ("")), (((($tmp =         // line 43
($context["accordion_item"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__content--accordion-item") : ("")), (((($tmp = (($_v2 =         // line 44
($context["element"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess && in_array($_v2::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v2["#module_package_listing"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#module_package_listing", [], "array", false, false, true, 44))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__content--package-listing") : (""))];
        // line 47
        yield "<details";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 47), "html", null, true);
        yield ">";
        // line 48
        if ((($tmp = ($context["title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 50
            $context["summary_classes"] = ["claro-details__summary", (((($tmp =             // line 52
($context["required"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("js-form-required") : ("")), (((($tmp =             // line 53
($context["required"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("form-required") : ("")), (((($tmp =             // line 54
($context["accordion"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__summary--accordion") : ("")), (((($tmp =             // line 55
($context["accordion_item"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__summary--accordion-item") : ("")), (((($tmp = (($_v3 =             // line 56
($context["element"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess && in_array($_v3::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v3["#module_package_listing"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#module_package_listing", [], "array", false, false, true, 56))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("claro-details__summary--package-listing") : (""))];
            // line 60
            yield "    <summary";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["summary_attributes"] ?? null), "addClass", [($context["summary_classes"] ?? null)], "method", false, false, true, 60), "html", null, true);
            yield ">";
            // line 61
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true);
            // line 62
            if ((($tmp = ($context["required"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 63
                yield "<span class=\"required-mark\"></span>";
            }
            // line 65
            yield "</summary>";
        }
        // line 67
        yield "<div";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content_attributes"] ?? null), "addClass", [($context["content_wrapper_classes"] ?? null)], "method", false, false, true, 67), "html", null, true);
        yield ">
    ";
        // line 68
        if ((($context["accordion"] ?? null) || ($context["accordion_item"] ?? null))) {
            // line 69
            yield "    <div";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(["class" => ($context["inner_wrapper_classes"] ?? null)]), "html", null, true);
            yield ">
    ";
        }
        // line 71
        yield "
      ";
        // line 72
        if ((($tmp = ($context["errors"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 73
            yield "        <div class=\"form-item form-item__error-message\">
          ";
            // line 74
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["errors"] ?? null), "html", null, true);
            yield "
        </div>
      ";
        }
        // line 77
        if ((($tmp = ($context["description"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 78
            yield "<div class=\"claro-details__description";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar((((($tmp = ($context["disabled"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (" is-disabled") : ("")));
            yield "\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["description"] ?? null), "html", null, true);
            yield "</div>";
        }
        // line 80
        if ((($tmp = ($context["children"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 81
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["children"] ?? null), "html", null, true);
        }
        // line 83
        if ((($tmp = ($context["value"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 84
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["value"] ?? null), "html", null, true);
        }
        // line 87
        if ((($context["accordion"] ?? null) || ($context["accordion_item"] ?? null))) {
            // line 88
            yield "    </div>
    ";
        }
        // line 90
        yield "  </div>
</details>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["accordion", "accordion_item", "element", "attributes", "title", "required", "summary_attributes", "content_attributes", "errors", "description", "disabled", "children", "value"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "core/themes/claro/templates/details.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  138 => 90,  134 => 88,  132 => 87,  129 => 84,  127 => 83,  124 => 81,  122 => 80,  115 => 78,  113 => 77,  107 => 74,  104 => 73,  102 => 72,  99 => 71,  93 => 69,  91 => 68,  86 => 67,  83 => 65,  80 => 63,  78 => 62,  76 => 61,  72 => 60,  70 => 56,  69 => 55,  68 => 54,  67 => 53,  66 => 52,  65 => 50,  63 => 48,  59 => 47,  57 => 44,  56 => 43,  55 => 42,  54 => 40,  52 => 36,  51 => 35,  50 => 34,  49 => 31,  47 => 27,  46 => 26,  45 => 25,  44 => 23,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "core/themes/claro/templates/details.html.twig", "/home/anurag/My-Projects/fossee/drupal10/web/core/themes/claro/templates/details.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 23, "if" => 48];
        static $filters = ["escape" => 47];
        static $functions = ["create_attribute" => 69];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape'],
                ['create_attribute'],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
