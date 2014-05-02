<?php

namespace Scaffold\Cli\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Scaffold\Scaffolder;

class ExecuteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Execute scaffold')
            ->addArgument('template_name', InputArgument::REQUIRED, 'Template name')
            ->addOption('output', 'out', InputOption::VALUE_OPTIONAL, 'Output path')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templatePath = $this->getContainer()->getParameter('scaffold.template_path');
        $templateName = $input->getArgument('template_name');

        if ($input->getOption('output')) {
            $outputPath = $input->getOption('output');
        } else {
            $outputPath = $this->getContainer()->getParameter('scaffold.output_path');
        }

        if (file_exists($templatePath . '/' . $templateName . '.yml')) {
            $fileLocator = new FileLocator(getcwd());
            $configFile = $fileLocator->locate($templatePath . '/' . $templateName . '.yml');
            $config = Yaml::parse($configFile);

            $templates = array_keys($config['templates']);
            foreach ($templates as $template) {
                $this->renderTemplate($template, $outputPath . '/' . $template);
            }
        } elseif (file_exists($templatePath . '/' . $templateName . '.twig')) {
            $this->renderTemplate($templateName, $outputPath . '/' . $templateName);
        } else {
            $output->writeln(sprintf("\n<error>Template %s doesn't exist.</error>\n", $templateName));
        }
    }

    private function registerVariables($scaffolder)
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.php')
            ->in($this->getContainer()->getParameter('scaffold.variables_path'));

        foreach ($finder as $file) {
            require_once $file->getRealpath();

            $className = str_replace('.php', '', $file->getRelativePathname());
            $classNameFqn = '\\' . $className;
            $variableName = lcfirst($className);

            $variable = new $classNameFqn;
            $scaffolder->register(array($variableName => $variable->render()));
        }

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
