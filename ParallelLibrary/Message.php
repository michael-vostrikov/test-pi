<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IMessage;

/**
 * Represents message used for communication between processes
 */
class Message implements IMessage
{
    /**
     * @var string message type
     */
    public $type;

    /**
     * @var string message data
     */
    public $data;


    /**
     * @param string message type
     * @param string message data
     */
    public function __construct($type, $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
