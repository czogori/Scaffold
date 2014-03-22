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

class ExecuteCommand extends Command
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

        $scaffolder = new Scaffolder();

        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../../../../temp/vars');

        foreach ($finder as $file) {
            require_once $file->getRealpath();

            $className = rtrim($file->getRelativePathname(), '.php');
            $classNameFqn = '\\' . $className;
            $variableName = lcfirst($className);

            $variable = new $classNameFqn;
            $scaffolder->register(array($variableName => $variable->render()));
        }

        $content = $scaffolder->scaffold();

        file_put_contents(__DIR__.'/../../../../out/test.php', $content);
    }
}

