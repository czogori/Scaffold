<?php

namespace Scaffold\Cli;

use Symfony\Component\Console\Application;

use Scaffold\Cli\Command\ExecuteCommand;

class ScaffoldApplication extends Application
{
    public function __construct()
    {
        parent::__construct('Scaffold', '0.1.0');

        $scaffold = new Scaffold();
        $container = $scaffold->getContainer();

        $this->addCommands(array(
            new ExecuteCommand('exe', $container),
        ));
    }
}
