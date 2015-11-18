<?php

namespace ParallelLibrary;

class Worker
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    const MESSAGE_DELIMITER = "\r\n";


    private $id;
    private $process;
    private $workerStreams;

    private $state;


    private $config = [
        'timeout' => 2000000,
    ];


    public function __construct($id, $config = [])
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->id = $id;
    }

    public function run($command)
    {
        $res = false;
        do {
            $streams = $this->createStreams();

            $process = proc_open($command, $streams['processStreams'], $pipes, null, null);
            if (!is_resource($process)) {
                throw new ParallelLibraryException("Cannot start child process. Command: $command");
            }
            $this->process = $process;
            $this->workerStreams = $streams['workerStreams'];

            $res = true;
        } while (false);

        return $res;
    }

    public function isRunning()
    {
        $processInfo = $this->getProcessInfo();
        return $processInfo['running'];
    }

    protected function createStreams()
    {
        $id = $this->id;
        $fileNames = [
            self::STDIN  => $id.'-in.pipe.txt',
            self::STDOUT => $id.'-out.pipe.txt',
            self::STDERR => $id.'-err.pipe.txt',
        ];

        $fileHandles = ['write' => [], 'read' => []];
        foreach ($fileNames as $streamID => $fileName) {
            $fileHandles['write'][$streamID] = $this->openFile($fileName, 'w');
            $fileHandles['read'][$streamID]  = $this->openFile($fileName, 'r');
        }

        $streams = [
            'processStreams' => [
                self::STDIN  => $fileHandles['read'][self::STDIN],
                self::STDOUT => $fileHandles['write'][self::STDOUT],
                self::STDERR => $fileHandles['write'][self::STDERR],
            ],
            'workerStreams'  => [
                self::STDIN  => $fileHandles['write'][self::STDIN],
                self::STDOUT => $fileHandles['read'][self::STDOUT],
                self::STDERR => $fileHandles['read'][self::STDERR],
            ],
        ];

        return $streams;
    }

    protected function openFile($filename, $mode)
    {
        $fileHandle = fopen($filename, $mode);
        if ($fileHandle === false) {
            throw new ParallelLibraryException("Cannot open file '$filename' with mode '$mode'");
        }

        return $fileHandle;
    }

    public function getProcessInfo()
    {
        return proc_get_status($this->process);
    }

    public function sendMessage($message)
    {
        $fileHandle = $this->workerStreams[self::STDIN];
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
        $fileHandle = $this->workerStreams[self::STDOUT];
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

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}
