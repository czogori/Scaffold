<?php

namespace Scaffold;

class Item
{
    public $isFile;
    public $path;
    public $template;

    public function __construct($path, $template = null, $isFile = true)
    {
        $this->path = $path;
        $this->template = $template;
        $this->isFile = $isFile;
    }
}

class ActionParser
{
    private $items = [];

    public function __construct($config)
    {
        $this->parse($config);
    }

    public function getItems()
    {
        return $this->items;
    }

    private function parse($node, $parent = '')
    {
        foreach($node as $key => $item) {
            $path = $parent . '/' . $key;
            if (is_array($item)) {
                $this->parse($item, $path);
            } elseif (null === $item) {
                $this->items[] = new Item($path, null, false);
            } else {
                $this->items[] = new Item($path, $item);
            }
        }
    }
}