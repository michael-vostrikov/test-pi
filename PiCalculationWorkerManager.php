<?php

use ParallelLibrary\WorkerManager;
use ParallelLibrary\interfaces\IWorker;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

/**
 * Represents parent process for calculation of PI number
 * @inheritdoc
 */
class PiCalculationWorkerManager extends WorkerManager
{
    /**
     * Message type, used for getting state of child process
     */
    const MESSAGE_TYPE_GET_STATE = 'GET_STATE';


    /**
     * Calculation start time
     */
    private $startTime;

    /**
     * Last known state of calculation in the child processes
     */
    private $workerStateList;


    /**
     * Sets start time and performs output initialization before running work
     */
    protected function runWork()
    {
        $this->startTime = microtime(true);
        $this->workerStateList = [];
        $this->setupOutput();

        parent::runWork();
    }

    /**
     * @inheritdoc
     * In worker command there is an random iteration count for calculation of PI by Monte-Carlo method in child process
     */
    protected function getWorkerCommand($_workerID)
    {
        $startupScript = __DIR__ .'/start-child-process.php';
        $processsClass = 'PiCalculationProcess';
        $iterationCount = $this->getIterationCount();

        return 'php ' .$startupScript .' ' .$processsClass .' ' .$iterationCount;
    }

    /**
     * Wait random time, then sends self::MESSAGE_TYPE_GET_STATE message to workers,
     * then output to browser summary information of PI calculation process by Monte-Carlo method
     * based on information from child processes.
     * Responses from child processes are handled in the function handleMessage()
     */
    protected function doWork()
    {
        $waitingTime = $this->getWaitingTime();
        usleep($waitingTime);

        $totalCircleHitCount = 0;
        $totalCount = 0;
        foreach ($this->workerList as $worker) {

            if ($worker->isRunning()) {
                $worker->sendMessage(new Message(self::MESSAGE_TYPE_GET_STATE));
            }

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

        echo 'time: ' .$timeDiff .' | ' .'pi: ' .$pi .'<br>';
    }

    /**
     * @inheritdoc
     * Only self::MESSAGE_TYPE_GET_STATE message is handled.
     * The handling refreshes worker state for current worker in worker state list
     */
    protected function handleMessage(IWorker $worker, IMessage $message)
    {
        switch ($message->type) {

            case self::MESSAGE_TYPE_GET_STATE:
                $this->workerStateList[$worker->getInternalID()] = $message->data;
                break;

            default:
                break;
        }
    }


    /**
     * Switches off output buffering, so the output will be send into browser immediately
     */
    private function setupOutput()
    {
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        while (@ob_end_clean());
        set_time_limit(-1);
    }

    /**
     * Returns random iteration count
     */
    private function getIterationCount()
    {
        return rand(100000, 200000);
    }

    /**
     * Returns random waiting time
     */
    private function getWaitingTime()
    {
        return rand(1*1000000, 2*1000000);
    }

    /**
     * Returns last known worker state from worker state list or null if it is unknown
     * @return array|null state of calculation in child process
     */
    private function getWorkerState(IWorker $worker)
    {
        $workerID = $worker->getInternalID();
        if (isset($this->workerStateList[$workerID])) {
            return $this->workerStateList[$workerID];
        }

        return null;
    }
}
