<?php

namespace Scaffold\Cli\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;
use Scaffold\Scaffolder;

class ExecuteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Execute scaffold')
            ->addArgument('template_name', InputArgument::REQUIRED, 'Template name')
            ->addOption('output', 'out', InputOption::VALUE_OPTIONAL, 'Output path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templateName = $input->getArgument('template_name');
        if ($input->getOption('output')) {
            $outputPath = $input->getOption('output') . '/' . $templateName;
        } else {
            $outputPath = $this->getContainer()->getParameter('scaffold.output_path') . '/' . $templateName;
        }

        $scaffolder = $this->getContainer()->get('scaffold.scaffolder');
        $scaffolder->setTemplate($templateName);

        $finder = new Finder();
        $finder->files()->in($this->getContainer()->getParameter('scaffold.variables_path'));

        foreach ($finder as $file) {
            require_once $file->getRealpath();

            $className = rtrim($file->getRelativePathname(), '.php');
            $classNameFqn = '\\' . $className;
            $variableName = lcfirst($className);

            $variable = new $classNameFqn;
            $scaffolder->register(array($variableName => $variable->render()));
        }

        $content = $scaffolder->scaffold();

        file_put_contents($outputPath, $content);
    }
}

