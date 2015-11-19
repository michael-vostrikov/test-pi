<?php

namespace ParallelLibrary;

class ParallelProcess
{
    private $messagingStrategy;

    public function __construct($messagingStrategy)
    {
        $this->messagingStrategy = $messagingStrategy;
    }

    public function run($arguments)
    {
    }

    public function sendMessage($message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }

    public function checkMessages()
    {
        $message = $this->receiveMessage();
        if ($message) {
            $this->handleMessage($message);
        }
    }

    public function handleMessage($message)
    {
    }
}
