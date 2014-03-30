<?php

namespace Scaffold;

class Scaffolder
{
    private $variables = array();
    private $templatePath;
    private $templateName;
    private $tmpPath;

    public function __construct($templatePath, $tmpPath)
    {
        $this->templatePath = $templatePath;
        $this->tmpPath = $tmpPath;
    }

    public function scaffold()
    {
        return $this->getCode();
    }

    public function setTemplate($templateName)
    {
        $this->templateName = $templateName . '.twig';
    }

    public function getCode()
    {
        $loader = new \Twig_Loader_Filesystem($this->templatePath);
        $twig = new \Twig_Environment($loader, array(
            'autoescape' => false,
            'strict_variables' => true,
            'debug' => true,
            'cache' => $this->tmpPath,
        ));
        $template = $twig->loadTemplate($this->templateName);

        return $template->render($this->variables);
    }

    public function register($variable)
    {
        $this->variables += $variable;
    }
}
