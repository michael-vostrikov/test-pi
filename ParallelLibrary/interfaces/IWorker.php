<?php

namespace ParallelLibrary\interfaces;

/**
 * Interface that should be implemented by classes who represents child process in parent process
 */
interface IWorker
{
    /**
     * Runs child process
     * @param string $command child process command line
     * @return bool
     */
    public function run($command);


    /**
     * Returns internal ID which identify worker in worker list
     * @return int internal ID
     */
    public function getInternalID();

    /**
     * Returns information about child process
     * @return array information about child process
     */
    public function getProcessInfo();

    /**
     * Returns a value indicating whether the current process is running
     * @return boolean whether the current process is running or not
     */
    public function isRunning();
}
