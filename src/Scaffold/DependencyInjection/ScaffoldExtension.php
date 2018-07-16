<?php

namespace Scaffold\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ScaffoldExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->defineParameters($container);

        $definition = new Definition('Scaffold\Scaffolder');
        $definition->setArguments(array('%scaffold.template_path%', '%scaffold.tmp_path%'));
        $definition->setShared(false);
        $container->setDefinition('scaffold.scaffolder', $definition);

        $definition = new Definition('Scaffold\Variable\Model');
        $container->setDefinition('model.variable', $definition);

    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'scaffold';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.example.com/symfony/schema/';
    }

    /**
     * Define parameters.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function defineParameters(ContainerBuilder $container)
    {
        $rootDirectory = getcwd();
        $container->setParameter('scaffold.template_path', $rootDirectory . '/temp');
        $container->setParameter('scaffold.tmp_path', '/tmp/scaffold');
        $container->setParameter('scaffold.variables_path', $rootDirectory . '/temp/vars');
        $container->setParameter('scaffold.output_path', $rootDirectory . '/out');
    }
}
