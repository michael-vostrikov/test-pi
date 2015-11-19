<?php

namespace ParallelLibrary;

class Worker
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    private $id;
    private $process;
    private $state;


    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run($command)
    {
        $res = false;
        do {
            $streams = $this->createStreams();

            $process = proc_open($command, $streams['processSideStreams'], $pipes, null, null);
            if (!is_resource($process)) {
                throw new ParallelLibraryException("Cannot start child process. Command: $command");
            }
            $this->process = $process;
            $this->messagingStrategy = new StreamMessagingStrategy($streams['workerSideStreams'][self::STDOUT], $streams['workerSideStreams'][self::STDIN]);

            $res = true;
        } while (false);

        return $res;
    }

    public function isRunning()
    {
        $processInfo = $this->getProcessInfo();
        return $processInfo['running'];
    }

    public function getProcessInfo()
    {
        return proc_get_status($this->process);
    }

    public function sendMessage($message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }


    private function createStreams()
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
            'processSideStreams' => [
                self::STDIN  => $fileHandles['read'][self::STDIN],
                self::STDOUT => $fileHandles['write'][self::STDOUT],
                self::STDERR => $fileHandles['write'][self::STDERR],
            ],
            'workerSideStreams'  => [
                self::STDIN  => $fileHandles['write'][self::STDIN],
                self::STDOUT => $fileHandles['read'][self::STDOUT],
                self::STDERR => $fileHandles['read'][self::STDERR],
            ],
        ];

        return $streams;
    }

    private function openFile($filename, $mode)
    {
        $fileHandle = fopen($filename, $mode);
        if ($fileHandle === false) {
            throw new ParallelLibraryException("Cannot open file '$filename' with mode '$mode'");
        }

        return $fileHandle;
    }
}
