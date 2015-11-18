<?php

namespace ParallelLibrary;

class Process
{
    const MESSAGE_DELIMITER = "\r\n";

    protected $config = [
        'timeout' => 2000000,
    ];


    public function __construct($config = [])
    {
        $this->config = array_replace_recursive($this->config, $config);
    }

    public function run($arguments)
    {
    }

    public function sendMessage($message)
    {
        $fileHandle = STDOUT;
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
        $fileHandle = STDIN;
        fseek($fileHandle, 0, SEEK_CUR);
        $serializedMessage = fgets($fileHandle);

        if (strpos($serializedMessage, self::MESSAGE_DELIMITER) === false && $waitForMessage) {
            $millisecondsInSecond = 1000000.0;
            $timeoutInSeconds = ((int)$this->config['timeout']) / $millisecondsInSecond;

            $startTime = microtime(true);
            while (microtime(true) - $startTime < $timeoutInSeconds) {
                fseek($fileHandle, 0, SEEK_CUR);
                $serializedMessage = fgets($fileHandle);
                if ($serializedMessage) {
                    break;
                }

                usleep(1000);
            }
        }

        $message = null;
        if (strpos($serializedMessage, self::MESSAGE_DELIMITER) !== false) {
            $message = json_decode($serializedMessage, true);
        }

        return $message;
    }
}
