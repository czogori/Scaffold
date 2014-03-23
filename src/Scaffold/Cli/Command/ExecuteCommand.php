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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = $input->getArguments();

        $scaffolder = $this->getContainer()->get('scaffold.scaffolder');

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

        file_put_contents($this->getContainer()->getParameter('scaffold.output_path') . '/test.php', $content);
    }
}

