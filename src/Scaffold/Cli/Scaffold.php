<?php

namespace Scaffold\Cli;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

use Scaffold\DependencyInjection\ScaffoldExtension;

class Scaffold
{
    private $container;

    public function __construct()
    {
        $container = new ContainerBuilder();

        $extensions = array(
            new ScaffoldExtension(),
        );
        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());
        }
        $container->compile();
        $this->container = $container;
    }

    public function get($service)
    {
        return $this->container->get($service);
    }

    public function getContainer()
    {
        return $this->container;
    }
}
