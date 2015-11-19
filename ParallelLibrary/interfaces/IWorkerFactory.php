<?php

namespace ParallelLibrary\interfaces;

/**
 * Interface that should be implemented by classes which are factory for creating workers
 */
interface IWorkerFactory
{
    /**
     * Creates and return worker
     * @param int $workerID internal worker ID
     */
    public function createWorker($workerID);
}
