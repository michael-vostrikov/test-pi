<?php

namespace ParallelLibrary\interfaces;

/**
 * Interface that should be implemented by classes who control workers representing child processes
 */
interface IWorkerManager
{
    /**
     * Runs worker manager
     */
    public function run();
}
