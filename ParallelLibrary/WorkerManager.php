<?php

namespace ParallelLibrary;

abstract class WorkerManager
{
    protected $workerList = [];

    protected $config = [
        'workerClassName' => '',
        'workerCount' => 0,
    ];


    public function __construct($config = [])
    {
        $this->config = array_replace_recursive($this->config, $config);
    }

    public function run()
    {
        $this->runWorkers();
        $this->runWork();
    }


    protected function runWorkers()
    {
        for ($i = 0; $i < (int)$this->config['workerCount']; $i++) {
            $worker = $this->createWorker($i);
            if ($worker->run($this->getWorkerCommand($i))) {
                $this->workerList[] = $worker;
            }
        }
    }

    protected function runWork()
    {
        while ($this->canWork()) {
            $this->doWork();
        }
    }

    protected function createWorker($id)
    {
        return new $this->config['workerClassName']($id);
    }

    protected function canWork()
    {
        $canWork = false;

        foreach ($this->workerList as $i => $worker) {
            if ($worker->isRunning()) {
                $canWork = true;
                break;
            }
        }

        return $canWork;
    }

    abstract protected function doWork();
    abstract protected function getWorkerCommand($workerID);
}
