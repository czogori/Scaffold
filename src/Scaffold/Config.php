<?php

namespace Scaffold;

class Config implements \ArrayAccess
{
    private $config = [];

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->config[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->config[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

    public  function getItems()
    {
        return $this->config;
    }
}
