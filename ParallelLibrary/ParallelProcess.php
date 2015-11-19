<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IParallelProcess;
use ParallelLibrary\interfaces\ICommunicable;
use ParallelLibrary\interfaces\IMessage;

/**
 * Represents child process in multi-process computing
 * Performs main work in child process
 */
abstract class ParallelProcess implements IParallelProcess, ICommunicable
{
    /**
     * @var \ParallelLibrary\interfaces\ICommunicable implementation of messaging strategy
     */
    private $messagingStrategy;


    /**
     * @param \ParallelLibrary\interfaces\ICommunicable implementation of messaging strategy
     * which allows the parent and child processes to communicate between each other
     */
    public function __construct(ICommunicable $messagingStrategy)
    {
        $this->messagingStrategy = $messagingStrategy;
    }


    /**
     * @inheritdoc
     */
    public function sendMessage(IMessage $message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    /**
     * @inheritdoc
     */
    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }


    /**
     * Gets and handle all messages which come from parent process
     * The function handleMessage() is called for every message
     */
    protected function processMessages()
    {
        while ($message = $this->receiveMessage()) {
            $this->handleMessage($message);
        }
    }


    /**
     * Handles the message which come from parent process
     * @param \ParallelLibrary\interfaces\IMessage $message message from parent process
     */
    abstract protected function handleMessage(IMessage $message);

    /**
     * @inheritdoc
     */
    abstract public function run($arguments);
}
