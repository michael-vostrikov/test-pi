<?php

namespace ParallelLibrary;

class Message
{
    public $type;
    public $data;

    public function __construct($type, $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
