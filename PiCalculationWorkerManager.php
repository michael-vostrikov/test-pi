<?php

use ParallelLibrary\WorkerManager;

class PiCalculationWorkerManager extends WorkerManager
{
    const MESSAGE_GET_STATE = 'GET_STATE';

    private $startTime;


    protected function runWork()
    {
        $this->startTime = microtime(true);
        $this->setupOutput();

        parent::runWork();
    }

    protected function getWorkerCommand($_workerID)
    {
        $processFile = __DIR__ .'/start-pi-calculation-process.php';
        $iterationCount = $this->getIterationCount();
        $arguments = $iterationCount;

        return 'php ' .$processFile .' ' .$arguments;
    }

    protected function doWork()
    {
        $waitingTime = $this->getWaitingTime();
        usleep($waitingTime);

        $totalCircleHitCount = 0;
        $totalCount = 0;
        foreach ($this->workerList as $worker)
        {
            $workerState = $this->getWorkerState($worker);
            if (!$workerState) continue;

            $totalCircleHitCount += $workerState['circleHitCount'];
            $totalCount += $workerState['currentIteration'];
        }

        $pi = 0;
        if ($totalCount != 0) {
            $pi = (4 * $totalCircleHitCount) / $totalCount;
        }
        $timeDiff = microtime(true) - $this->startTime;

        echo $timeDiff .' ' .$pi .'<br>';
    }

    private function setupOutput()
    {
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        while (@ob_end_clean());
        set_time_limit(-1);
    }

    private function getIterationCount()
    {
        return rand(100000, 200000);
    }

    private function getWaitingTime()
    {
        return rand(1*1000000, 2*1000000);
    }

    private function getWorkerState($worker)
    {
        if (!$worker->isRunning()) {
            $state = $worker->getState();
            if (!$state) {
                $state = $worker->receiveMessage($waitForMessage = false);
            }

            return $state;
        }

        $worker->sendMessage(self::MESSAGE_GET_STATE);
        $state = $worker->receiveMessage($waitForMessage = false);
        $worker->setState($state);

        return $state;
    }
}
