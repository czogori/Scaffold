<?php

namespace Scaffold\Cli;

use Symfony\Component\Console\Application;

use Scaffold\Cli\Command\ExecuteCommand;

class ScaffoldApplication extends Application
{
    public function __construct()
    {
        parent::__construct('Scaffold', '0.1.0');

        $this->addCommands(array(
            new ExecuteCommand('exe'),
        ));
    }
}
