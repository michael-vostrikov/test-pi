<?php

namespace ParallelLibrary\interfaces;

/**
 * Interface that should be implemented by classes who performs main work in the child process
 */
interface IParallelProcess
{
    /**
     * Runs main work in the child process
     * @param array $arguments arguments of process, usually arguments from command line
     */
    public function run($arguments);
}
