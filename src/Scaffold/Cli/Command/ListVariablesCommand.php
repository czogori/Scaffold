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

class ListVariablesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('List available variables.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.php')
            ->in($this->getContainer()->getParameter('scaffold.variables_path'));

        $output->writeln("\n<comment>List available variables.</comment>\n");
        foreach ($finder as $file) {
            require_once $file->getRealpath();

            $className = str_replace('.php', '', $file->getRelativePathname());
            $classNameFqn = '\\' . $className;
            $variableName = lcfirst($className);

            $variable = new $classNameFqn;
            $output->writeln(sprintf('<info>%s</info> %s', $variable->getName(), $variable->getDescription()));
        }
    }
}
