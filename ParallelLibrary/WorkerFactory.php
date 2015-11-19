<?php

namespace ParallelLibrary;

class WorkerFactory
{
    public function createWorker($id)
    {
        return new Worker($id);
    }
}
