<?php

namespace Scaffold;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Scaffold
{
    private $custom;

    public function __construct($configurationPath, $outputPath, $scaffolder)
    {
        $this->configurationPath = $configurationPath;
        $this->outputPath = $outputPath;
        $this->varPath = dirname($configurationPath) . '/vars';
        $this->scaffolder = $scaffolder;
        $this->config = new Config();
    }

    public function scaffold()
    {
        $this->registerVariables();

        $fs = new Filesystem();
        $config = $this->getItems($this->configurationPath);
        $actionParser = new ActionParser($config);

        foreach ($actionParser->getItems() as $item) {
            $path  = $this->outputPath . $item->path;
            if (strpos($item->path, '%') !== false) {
                foreach ($this->config->getItems() as $name => $value) {
                    if (!is_array($value)) {
                        $path = str_replace('%' . $name . '%', $value, $path);
                    }

                }
            }

            if (strpos($item->path, '@custom') !== false) {
                list(,, $code) = explode('/', $item->path);
                $this->custom[$code] = $this->renderTemplate($item->template);
                continue;
            }

            if ($item->isFile) {
                $fs->mkdir(dirname($path));

                $this->renderTemplate($item->template);
                file_put_contents($path, $this->renderTemplate($item->template));
            } else {
                $fs->mkdir($path);
            }
        }
    }

    public function getCustoms()
    {
        return $this->custom;
    }

    public function addVar($name, $value)
    {
        $this->config->offsetSet($name, $value);
    }

    public function addVars(array $vars = [])
    {
        foreach ($vars as $name => $value) {
            $this->config->offsetSet($name, $value);
        }
    }

    private function registerVariables()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.php')
            ->in($this->varPath);

        $this->scaffolder->register(array('config' => $this->config));
        foreach ($finder as $file) {
            require_once $file->getRealpath();

            $className = str_replace('.php', '', $file->getRelativePathname());
            $classNameFqn = '\\' . $className;
            $variableName = lcfirst($className);

            $variable = new $classNameFqn;
            $this->scaffolder->register(array($variableName => $variable->render()));
        }

    }

    private function renderTemplate($templateName)
    {
        $this->scaffolder->setTemplate($templateName);
        return $this->scaffolder->scaffold();
    }

    private function getItems($templatePath)
    {
        if (file_exists($templatePath)) {
            return Yaml::parse(file_get_contents($templatePath));
        } else {
            throw new \Exception('Brak pliku ' . $templatePath);
        }
    }
}
