<?php

namespace Scaffold\Variable;

use Scaffold\Variable\Model\Column;

class Model 
{
    public function getName()
    {
        return 'model';
    }

    public function render()
    {
        $columns = array(
            new Column('name', 'string'),
            new Column('age', 'integer'),
        );
        $object = new \StdClass;

        $object->columns = $columns;

        return $object;
    }


    public function getDescription()
    {
        return 'Model';
    }
}
