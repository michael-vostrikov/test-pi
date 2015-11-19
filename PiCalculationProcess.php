<?php

use ParallelLibrary\ParallelProcess;

class PiCalculationProcess extends ParallelProcess
{
    const MESSAGE_GET_STATE = 'GET_STATE';

    private $iterationCount;
    private $currentIteration;
    private $circleHitCount;


    public function run($arguments)
    {
        if (count($arguments) == 0) {
            return;
        }

        $iterationCount = (int)$arguments[0];
        $this->calculatePiByMonteCarloMethod($iterationCount);
    }

    public function handleMessage($message)
    {
        switch ($message) {

            case self::MESSAGE_GET_STATE:
                $this->sendState();
                break;

            default:
                break;
        }
    }


    private function calculatePiByMonteCarloMethod($iterationCount)
    {
        $this->circleHitCount = 0;
        $this->iterationCount = $iterationCount;
        for ($i = 1; $i <= $this->iterationCount; $i++) {
            $x = mt_rand() / mt_getrandmax();
            $y = mt_rand() / mt_getrandmax();

            if (self::inCircle($x, $y)) {
                $this->circleHitCount++;
            }


            // for better performance we can check messages not on every iteration, but e.g. on every 1000 iteration
            $this->currentIteration = $i;
            $this->processMessages();
        }

        $this->sendState();
    }

    private static function inCircle($x, $y)
    {
        return (($x * $x + $y * $y) < 1.0);
    }

    private function sendState()
    {
        $state = [
            'iterationCount' => $this->iterationCount,
            'currentIteration' => $this->currentIteration,
            'circleHitCount' => $this->circleHitCount,
        ];
        $this->sendMessage($state);
    }
}
