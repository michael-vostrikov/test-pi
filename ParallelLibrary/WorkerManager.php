<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IWorkerManager;
use ParallelLibrary\interfaces\IWorkerFactory;
use ParallelLibrary\interfaces\IWorker;
use ParallelLibrary\interfaces\IMessage;

/**
 * Represents parent process in multi-process computing
 * Creates and manages workers
 * For using it you have to extends from it and implement all abstract methods
 * Any protected methods can be overriden for implementing specific functionality
 */
abstract class WorkerManager implements IWorkerManager
{
    /**
     * @var array list of workers
     */
    protected $workerList = [];

    /**
     * @var \ParallelLibrary\interfaces\IWorkerFactory factory for creating workers
     */
    protected $workerFactory;

    /**
     * @var array configuration parameters
     */
    protected $config = [
        'workerCount' => 0,
    ];


    /**
     * @param array $config configuration parameters
     * @param \ParallelLibrary\interfaces\IWorkerFactory $workerFactory factory for creating workers
     */
    public function __construct($config, IWorkerFactory $workerFactory)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->workerFactory = $workerFactory;
    }

    /**
     * Runs workers depending on configuration and then runs main working loop
     */
    public function run()
    {
        $this->runWorkers();
        $this->runWork();
    }


    /**
     * Runs workers depending on configuration and adds them to worker list
     */
    protected function runWorkers()
    {
        for ($i = 0; $i < (int)$this->config['workerCount']; $i++) {
            $worker = $this->workerFactory->createWorker($i);
            if ($worker->run($this->getWorkerCommand($i))) {
                $this->workerList[] = $worker;
            }
        }
    }

    /**
     * Runs main loop which handle the messages from child processes by calling function handleWorkerMessages()
     * and call doWork() function in every iteration
     * The loop is runnning while canWork() function returns true
     * After the end of loop all all remaining messages are handled
     */
    protected function runWork()
    {
        while ($this->canWork()) {
            $this->handleWorkerMessages();
            $this->doWork();
        }
        $this->handleWorkerMessages();
    }

    /**
     * Returns value indicating that the main loop in function runWork() can be continued
     * This implementation allow to work while at least one child process is running
     * @return bool
     */
    protected function canWork()
    {
        $canWork = false;

        foreach ($this->workerList as $worker) {
            if ($worker->isRunning()) {
                $canWork = true;
                break;
            }
        }

        return $canWork;
    }

    /**
     * Gets and handle all messages which come from child processes
     * The function handleMessage() is called for every message
     */
    protected function handleWorkerMessages()
    {
        foreach ($this->workerList as $worker) {
            while ($message = $worker->receiveMessage()) {
                $this->handleMessage($worker, $message);
            }
        }
    }


    /**
     * Returns command which will be used in worker to run child process
     * @param int $workerID internal worker id
     */
    abstract protected function getWorkerCommand($workerID);

    /**
     * Handles the message which come from worker
     * @param \ParallelLibrary\interfaces\IWorker $worker worker representing child process
     * @param \ParallelLibrary\interfaces\IMessage $message message from child process
     */
    abstract protected function handleMessage(IWorker $worker, IMessage $message);

    /**
     * Function that is called every iteration in main loop
     */
    abstract protected function doWork();
}
