<?php

namespace ParallelLibrary\interfaces;

interface ICommunicable
{
    public function sendMessage(IMessage $message);
    public function receiveMessage();
}
