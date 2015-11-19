<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IParallelProcess;
use ParallelLibrary\interfaces\ICommunicable;
use ParallelLibrary\interfaces\IMessage;


abstract class ParallelProcess implements IParallelProcess, ICommunicable
{
    private $messagingStrategy;

    public function __construct(ICommunicable $messagingStrategy)
    {
        $this->messagingStrategy = $messagingStrategy;
    }

    public function run($arguments)
    {
    }

    public function sendMessage(IMessage $message)
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

    abstract protected function handleMessage(IMessage $message);
}
