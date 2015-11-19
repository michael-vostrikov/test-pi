<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\ICommunicable;
use ParallelLibrary\interfaces\IParallelProcess;

abstract class ParallelProcess implements IParallelProcess
{
    private $messagingStrategy;

    public function __construct(ICommunicable $messagingStrategy)
    {
        $this->messagingStrategy = $messagingStrategy;
    }

    public function run($arguments)
    {
    }

    public function sendMessage(Message $message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }

    protected function processMessages()
    {
        while ($message = $this->receiveMessage()) {
            $this->handleMessage($message);
        }
    }

    abstract protected function handleMessage(Message $message);
}
