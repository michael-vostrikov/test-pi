<?php

namespace ParallelLibrary\interfaces;

use ParallelLibrary\Message;

interface ICommunicable
{
    public function sendMessage(Message $message);
    public function receiveMessage();
}
