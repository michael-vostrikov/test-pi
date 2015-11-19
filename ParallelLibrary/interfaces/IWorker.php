<?php

namespace ParallelLibrary\interfaces;

interface IWorker
{
    public function run($command);
    public function isRunning();
    public function getProcessInfo();
    public function getInternalID();
}
