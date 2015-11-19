<?php

namespace ParallelLibrary\interfaces;

/**
 * Interface that should be implemented by classes who allow to send and receive messages
 */
interface ICommunicable
{
    /**
     * Sends message
     * @param ParallelLibrary\interfaces\IMessage $message
     * @return bool success or fail
     */
    public function sendMessage(IMessage $message);

    /**
     * Receives message
     * @return ParallelLibrary\interfaces\IMessage|null message if it has been read or null if there is no mesaages
     */
    public function receiveMessage();
}
