<?php

use ParallelLibrary\ParallelProcess;
use ParallelLibrary\Message;

class PingPongProcess extends ParallelProcess
{
    const MESSAGE_TYPE_PING = 'PING';
    const MESSAGE_TYPE_PONG = 'PONG';
    const MESSAGE_TYPE_TIRED = 'Sorry, I am tired';

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

    public function handleMessage(Message $message)
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
