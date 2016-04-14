<?php

namespace Scaffold\Cli\Command;

use Scaffold\Scaffold;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;




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
            ->addArgument('user_input', InputArgument::OPTIONAL, 'User input')
            ->addOption('output', 'out', InputOption::VALUE_OPTIONAL, 'Output path')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scaffolder = $this->getContainer()->get('scaffold.scaffolder');
        $configurationPath = $this->getContainer()->getParameter('scaffold.template_path') . '/' .
            $input->getArgument('template_name') . '.yml';
        $userInput = $input->getArgument('user_input');

        $outputPath = $input->getOption('output') ?
            $input->getOption('output') : $this->getContainer()->getParameter('scaffold.output_path');

        $varPath = $this->getContainer()->getParameter('scaffold.variables_path');

        (new Scaffold($configurationPath, $outputPath, $scaffolder))->scaffold();

    }
}
