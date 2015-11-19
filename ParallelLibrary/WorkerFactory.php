<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IWorkerFactory;

/**
 * Factory for creating workers
 */
class WorkerFactory implements IWorkerFactory
{
    /**
     * @inheritdoc
     */
    public function createWorker($workerID)
    {
        return new Worker($workerID);
    }
}
