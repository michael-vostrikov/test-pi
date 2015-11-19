<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IWorker;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\interfaces\ICommunicable;

/**
 * Represents child process in the parent process
 * Used for creating and controlling child process
 */
class Worker implements IWorker, ICommunicable
{
    /**
     * Pipe IDs
     */
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    /**
     * @var int internal ID. Usually it is index in worker list
     */
    private $internalID;

    /**
     * @var resource Child process handle
     */
    private $process;

    /**
     * @var ICommunicable implementation of messaging strategy
     * which allows the parent and child processes to communicate between each other
     */
    private $messagingStrategy;


    /**
     * @param int $internalID some number for identifying worker
     */
    public function __construct($internalID)
    {
        $this->internalID = $internalID;
    }

    /**
     * @inheritdoc
     */
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


    /**
     * @inheritdoc
     */
    public function getInternalID()
    {
        return $this->internalID;
    }

    /**
     * @inheritdoc
     */
    public function getProcessInfo()
    {
        return proc_get_status($this->process);
    }

    /**
     * @inheritdoc
     */
    public function isRunning()
    {
        $processInfo = $this->getProcessInfo();
        return $processInfo['running'];
    }


    /**
     * @inheritdoc
     */
    public function sendMessage(IMessage $message)
    {
        return $this->messagingStrategy->sendMessage($message);
    }

    /**
     * @inheritdoc
     */
    public function receiveMessage()
    {
        return $this->messagingStrategy->receiveMessage();
    }


    /**
     * Creates the pipes to be used for process communication
     * @return array
     * [
     *     'processSideStreams' => [self::STDIN => resource(read),  self::STDOUT => resource(write), self::STDERR => resource(write)],
     *     'workerSideStreams'  => [self::STDIN => resource(write), self::STDOUT => resource(read),  self::STDERR => resource(read)],
     * ]
     * If the stream in processSideStreams is opened for read, then related stream in workerSideStreams is opened for write
     */
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

    /**
     * Opens the file
     * @param string $fileName file name for opening
     * @param string $mode mode for function fopen()
     * @return resource file handle
     */
    private function openFile($fileName, $mode)
    {
        $fileHandle = fopen($fileName, $mode);
        if ($fileHandle === false) {
            throw new ParallelLibraryException("Cannot open file '$fileName' with mode '$mode'");
        }

        return $fileHandle;
    }
}
