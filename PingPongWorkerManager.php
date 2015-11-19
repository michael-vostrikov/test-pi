<?php

use ParallelLibrary\WorkerManager;
use ParallelLibrary\interfaces\IWorker;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

class PingPongWorkerManager extends WorkerManager
{
    const MESSAGE_TYPE_PING = 'PING';
    const MESSAGE_TYPE_PONG = 'PONG';

    protected function getWorkerCommand($_workerID)
    {
        $startupScript = __DIR__ .'/start-child-process.php';
        $processsClass = 'PingPongProcess';

        return 'php ' .$startupScript .' ' .$processsClass;
    }

    protected function runWorkers()
    {
        $this->setupOutput();

        parent::runWorkers();

        foreach ($this->workerList as $worker) {
            $this->sendPing($worker);
        }
    }

    protected function doWork()
    {
    }

    protected function handleMessage(IWorker $worker, IMessage $message)
    {
        echo 'Receive: '.$message->type.' (workerID = '.$worker->getInternalID().')<br>';

        switch ($message->type) {

            case self::MESSAGE_TYPE_PONG:
                $this->sendPing($worker);
                break;

            default:
                break;
        }
    }

    private function sendPing($worker)
    {
        $message = new Message(self::MESSAGE_TYPE_PING);
        $worker->sendMessage($message);
        echo 'Send: '.$message->type.' (workerID = '.$worker->getInternalID().')<br>';
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
}
