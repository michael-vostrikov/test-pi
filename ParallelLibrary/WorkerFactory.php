<?php

namespace ParallelLibrary;

use ParallelLibrary\interfaces\IWorkerFactory;

class WorkerFactory implements IWorkerFactory
{
    public function createWorker($workerID)
    {
        return new Worker($workerID);
    }
}
