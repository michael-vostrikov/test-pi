<?php

namespace ParallelLibrary\interfaces;

interface IWorkerFactory
{
    public function createWorker($workerID);
}
