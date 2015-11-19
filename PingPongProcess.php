<?php

use ParallelLibrary\ParallelProcess;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

class PingPongProcess extends ParallelProcess
{
    const MESSAGE_TYPE_PING = 'PING';
    const MESSAGE_TYPE_PONG = 'PONG';
    const MESSAGE_TYPE_TIRED = "I am tired, let's stop";

    private $currentCount;
    private $maxCount;


    public function run($arguments)
    {
        $this->currentCount = 0;
        $this->maxCount = rand(1, 10);

        $this->playPingPong();
    }


    private function playPingPong()
    {
        while ($this->currentCount < $this->maxCount) {
            $this->processMessages();
        }

        $this->sendMessage(new Message(self::MESSAGE_TYPE_TIRED));
    }

    public function handleMessage(IMessage $message)
    {
        switch ($message->type) {

            case self::MESSAGE_TYPE_PING:
                sleep(1);
                $this->currentCount++;
                $this->sendMessage(new Message(self::MESSAGE_TYPE_PONG));
                break;

            default:
                break;
        }
    }
}
