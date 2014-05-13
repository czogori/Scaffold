<?php

namespace Scaffold\Cli\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ModelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a model.')
            ->addArgument('variables', InputArgument::IS_ARRAY, 'Specify your variables!')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templatePath = $this->getContainer()->getParameter('scaffold.template_path');
        $outputPath = $this->getContainer()->getParameter('scaffold.output_path');

        if (file_exists($templatePath . '/' . 'model.yml')) {
            $config = Yaml::parse($templatePath . '/' . 'model.yml');

            $this->renderTemplate('model', $outputPath . '/' . 'model');
        }    
    }

    private function registerVariables($scaffolder)
    {
        $modelVar = $this->getContainer()->get('model.variable');
        $scaffolder->register(array('model' => $modelVar->render()));
    }

    private function renderTemplate($templateName, $outputPath)
    {
        $scaffolder = $this->getContainer()->get('scaffold.scaffolder');
        $scaffolder->setTemplate($templateName);
        $this->registerVariables($scaffolder);
        $content = $scaffolder->scaffold();

        file_put_contents($outputPath, $content);
    }
}
