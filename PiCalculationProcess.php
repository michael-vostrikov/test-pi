<?php

use ParallelLibrary\ParallelProcess;
use ParallelLibrary\interfaces\IMessage;
use ParallelLibrary\Message;

/**
 * Represents child process for calculation of PI number
 * @inheritdoc
 */
class PiCalculationProcess extends ParallelProcess
{
    /**
     * Message type, used for returning state of calculation
     */
    const MESSAGE_TYPE_GET_STATE = 'GET_STATE';


    /**
     * @var int total iteration count
     */
    private $iterationCount;

    /**
     * @var int current iteration of calculation
     */
    private $currentIteration;

    /**
     * @var int count of point which hit the circle area
     */
    private $circleHitCount;


    /**
     * Gets iteration count from arguments and runs calculation of PI number by Monte-Carlo method
     */
    public function run($arguments)
    {
        if (count($arguments) == 0) {
            return;
        }

        $iterationCount = (int)$arguments[0];
        $this->calculatePiByMonteCarloMethod($iterationCount);
    }


    /**
     * Calculation of PI number by Monte-Carlo method
     * Performs calculation and also calls processMessages() during calculation to handle messages which come from parent process
     */
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

    /**
     * Returns a value indicating if the point [x, y] is in the area of circle with radius = 1
     * @param float $x
     * @param float $y
     * @return bool
     */
    private static function inCircle($x, $y)
    {
        return (($x * $x + $y * $y) < 1.0);
    }


    /**
     * @inheritdoc
     * Only self::MESSAGE_TYPE_GET_STATE message is handled.
     * The handling sends state of calculation to parent process
     */
    public function handleMessage(IMessage $message)
    {
        switch ($message->type) {

            case self::MESSAGE_TYPE_GET_STATE:
                $this->sendState();
                break;

            default:
                break;
        }
    }

    /**
     * Sends state of calculation to parent process
     */
    private function sendState()
    {
        $state = [
            'iterationCount' => $this->iterationCount,
            'currentIteration' => $this->currentIteration,
            'circleHitCount' => $this->circleHitCount,
        ];

        $this->sendMessage(new Message(self::MESSAGE_TYPE_GET_STATE, $state));
    }
}
