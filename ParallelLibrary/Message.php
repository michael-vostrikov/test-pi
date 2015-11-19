<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IMessage;


class Message implements IMessage
{
    public $type;
    public $data;

    public function __construct($type, $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
