<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\ICommunicable;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

/**
 * Messaging system which allows some processes to communicate between each other using PHP streams
 */
class StreamMessagingStrategy implements ICommunicable
{
    /**
     * Delimiter which signs the end oof message
     */
    const MESSAGE_DELIMITER = "\r\n";


    /**
     * Stream which the messages will be read from
     */
    private $inputStream;

    /**
     * Stream which the messages will be write to
     */
    private $outputStream;


    /**
     * @param resource $inputStream stream which the messages will be read from
     * @param resource $outputStream stream which the messages will be write to
     */
    public function __construct($inputStream, $outputStream)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
    }

    /**
     * Sends the message into output stream
     * @inheritdoc
     */
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

    /**
     * Receives the message from input stream
     * @inheritdoc
     */
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
