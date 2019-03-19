<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @FOSUser/Security/login_content.html.twig */
class __TwigTemplate_cfd36bf25cd71db0daf9538514d4b47be28d486b72dcc944b6cb8aecb49db081 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@FOSUser/Security/login_content.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@FOSUser/Security/login_content.html.twig"));

        // line 2
        if ((isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 2, $this->source); })())) {
            // line 3
            echo "    <div class=\"alert alert-danger\">
        ";
            // line 4
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans(twig_get_attribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 4, $this->source); })()), "messageKey", []), twig_get_attribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 4, $this->source); })()), "messageData", []), "security"), "html", null, true);
            echo "
    </div>
";
        }
        // line 7
        echo "<form action=\"";
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("fos_user_security_check");
        echo "\" method=\"post\">
    ";
        // line 8
        if ((isset($context["csrf_token"]) || array_key_exists("csrf_token", $context) ? $context["csrf_token"] : (function () { throw new RuntimeError('Variable "csrf_token" does not exist.', 8, $this->source); })())) {
            // line 9
            echo "        <input type=\"hidden\" name=\"_csrf_token\" value=\"";
            echo twig_escape_filter($this->env, (isset($context["csrf_token"]) || array_key_exists("csrf_token", $context) ? $context["csrf_token"] : (function () { throw new RuntimeError('Variable "csrf_token" does not exist.', 9, $this->source); })()), "html", null, true);
            echo "\"/>
    ";
        }
        // line 11
        echo "    <div class=\"container\">
        <div class=\"row\">
            <div class=\"col-md-3\"></div>
            <div class=\"col-md-6\">
                <div class=\"bg-light py3 px-3\">
                    <h1>Login In !</h1>
                    <form action=\"\" method=\"post\">
                        <div class=\"form-group\">
                            <label for=\"username\">
                                ";
        // line 20
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("security.login.username", [], "FOSUserBundle"), "html", null, true);
        echo "</label>
                            <input type=\"text\" class=\"form-control\" id=\"username\" placeholder=\"Entrer Votre email\" name=\"_username\" value=\"";
        // line 21
        echo twig_escape_filter($this->env, (isset($context["last_username"]) || array_key_exists("last_username", $context) ? $context["last_username"] : (function () { throw new RuntimeError('Variable "last_username" does not exist.', 21, $this->source); })()), "html", null, true);
        echo "\" required=\"required\" autocomplete=\"username\"/>
                        </div>
                        <div class=\"form-group\">
                            <label for=\"password\">Password :</label>
                            <input autocomplete=\"current-password\" class=\"form-control\" id=\"password\" name=\"_password\" placeholder=\"Entrer Votre mot de passe\" required=\"required\" type=\"password\"/>
                            <input id=\"remember_me\" name=\"_remember_me\" type=\"checkbox\" value=\"on\"/>
                            <label for=\"remember_me\">
                                ";
        // line 28
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("security.login.remember_me", [], "FOSUserBundle"), "html", null, true);
        echo "</label>
                        </div>
                        <div class=\"form-group\">
                            <input class=\"btn btn-success\" type=\"submit\" id=\"_submit\" name=\"_submit\" value=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("security.login.submit", [], "FOSUserBundle"), "html", null, true);
        echo "\"/>
                        </div>
                    </form>
                </div>
            </div>
            <div class=\"col-md-3\"></div>
        </div>
    </div>
</form>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "@FOSUser/Security/login_content.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  96 => 31,  90 => 28,  80 => 21,  76 => 20,  65 => 11,  59 => 9,  57 => 8,  52 => 7,  46 => 4,  43 => 3,  41 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{% trans_default_domain 'FOSUserBundle' %}
{% if error %}
    <div class=\"alert alert-danger\">
        {{ error.messageKey|trans(error.messageData, 'security') }}
    </div>
{% endif %}
<form action=\"{{ path(\"fos_user_security_check\") }}\" method=\"post\">
    {% if csrf_token %}
        <input type=\"hidden\" name=\"_csrf_token\" value=\"{{ csrf_token }}\"/>
    {% endif %}
    <div class=\"container\">
        <div class=\"row\">
            <div class=\"col-md-3\"></div>
            <div class=\"col-md-6\">
                <div class=\"bg-light py3 px-3\">
                    <h1>Login In !</h1>
                    <form action=\"\" method=\"post\">
                        <div class=\"form-group\">
                            <label for=\"username\">
                                {{ 'security.login.username'|trans }}</label>
                            <input type=\"text\" class=\"form-control\" id=\"username\" placeholder=\"Entrer Votre email\" name=\"_username\" value=\"{{ last_username }}\" required=\"required\" autocomplete=\"username\"/>
                        </div>
                        <div class=\"form-group\">
                            <label for=\"password\">Password :</label>
                            <input autocomplete=\"current-password\" class=\"form-control\" id=\"password\" name=\"_password\" placeholder=\"Entrer Votre mot de passe\" required=\"required\" type=\"password\"/>
                            <input id=\"remember_me\" name=\"_remember_me\" type=\"checkbox\" value=\"on\"/>
                            <label for=\"remember_me\">
                                {{ 'security.login.remember_me'|trans }}</label>
                        </div>
                        <div class=\"form-group\">
                            <input class=\"btn btn-success\" type=\"submit\" id=\"_submit\" name=\"_submit\" value=\"{{ 'security.login.submit'|trans }}\"/>
                        </div>
                    </form>
                </div>
            </div>
            <div class=\"col-md-3\"></div>
        </div>
    </div>
</form>", "@FOSUser/Security/login_content.html.twig", "D:\\laragon\\www\\mybestquotes\\templates\\bundles\\FOSUserBundle\\Security\\login_content.html.twig");
    }
}
