<?php

namespace ParallelLibrary;

class StreamMessagingStrategy
{
    const MESSAGE_DELIMITER = "\r\n";


    private $inputStream;
    private $outputStream;


    public function __construct($inputStream, $outputStream)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
    }

    public function sendMessage($message)
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

    public function receiveMessage($waitForMessage = false)
    {
        $fileHandle = $this->inputStream;
        fseek($fileHandle, 0, SEEK_CUR);
        $serializedMessage = fgets($fileHandle);

        $message = null;
        if (strpos($serializedMessage, self::MESSAGE_DELIMITER) !== false) {
            $message = json_decode($serializedMessage, true);
        }

        return $message;
    }
}
