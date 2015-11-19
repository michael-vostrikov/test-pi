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

    public function processMessages()
    {
        while ($message = $this->receiveMessage()) {
            $this->handleMessage($message);
        }
    }

    public function handleMessage($message)
    {
    }
}
