<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\ICommunicable;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

class StreamMessagingStrategy implements ICommunicable
{
    const MESSAGE_DELIMITER = "\r\n";


    private $inputStream;
    private $outputStream;


    public function __construct($inputStream, $outputStream)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
    }

    public function sendMessage(IMessage $message)
    {
        $fileHandle = $this->outputStream;
        $serializedMessage = json_encode($message) .self::MESSAGE_DELIMITER;
        $res = fwrite($fileHandle, $serializedMessage);
        fflush($fileHandle);

        if ($res === false) {
            return false;
        }

        return true;
    }

    public function receiveMessage()
    {
        $fileHandle = $this->inputStream;
        fseek($fileHandle, 0, SEEK_CUR);                // reset EOF information
        $serializedMessage = fgets($fileHandle);

        $message = null;
        if (strpos($serializedMessage, self::MESSAGE_DELIMITER) !== false) {
            $messageParams = json_decode($serializedMessage, true);

            $message = new Message($messageParams['type'], $messageParams['data']);
        }

        return $message;
    }
}
