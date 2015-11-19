<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IWorker;
use ParallelLibrary\interfaces\ICommunicable;

class Worker implements IWorker, ICommunicable
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    private $internalID;
    private $process;


    public function __construct($internalID)
    {
        $this->internalID = $internalID;
    }

    public function run($command)
    {
        $streams = $this->createPipeStreams();

        $process = proc_open($command, $streams['processSideStreams'], $pipes, null, null);
        if (!is_resource($process)) {
            throw new ParallelLibraryException("Cannot start child process. Command: $command");
        }

        $this->process = $process;
        $this->messagingStrategy = new StreamMessagingStrategy($streams['workerSideStreams'][self::STDOUT], $streams['workerSideStreams'][self::STDIN]);

        return true;
    }

    public function getProcessInfo()
    {
        return proc_get_status($this->process);
    }

    public function isRunning()
    {
        $processInfo = $this->getProcessInfo();
        return $processInfo['running'];
    }

    public function getInternalID()
    {
        return $this->internalID;
    }


    public function sendMessage(Message $message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }


    private function createPipeStreams()
    {
        $id = $this->internalID;
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
