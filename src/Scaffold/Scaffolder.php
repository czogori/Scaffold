<?php

namespace Scaffold;

class Scaffolder
{
	private $variables = array();
    private $templatePath;

    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
    }

	public function scaffold()
	{
		return $this->getCode();
	}

	public function getCode()
    {
    	$loader = new \Twig_Loader_Filesystem($this->templatePath);
        $twig = new \Twig_Environment($loader, array(
            'autoescape' => false,
            'strict_variables' => true,
            'debug' => true,
            'cache' => '/tmp/scaffold',
        ));

        //$this->addTwigExtensions($twig, $loader);
        //$this->addTwigFilters($twig);
        $template = $twig->loadTemplate('test.php');
        return $template->render($this->variables);
    }

    public function register($variable)
    {
    	$this->variables += $variable;
    }
}
