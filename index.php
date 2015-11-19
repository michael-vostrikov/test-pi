<?php

require_once('autoload.php');


$config = [
    'workerCount' => 2,
];

$workerFactory = new \ParallelLibrary\WorkerFactory();
$workerManager = new PiCalculationWorkerManager($config, $workerFactory);
$workerManager->run();
