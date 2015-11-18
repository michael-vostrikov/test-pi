<?php

require_once('autoload.php');



$config = [
    'childProcessStartupScript' => 'start-child-process.php',
    'processClassName' => 'PiCalculationProcess.php',
    'workerClassName' => '\ParallelLibrary\Worker',
    'workerCount' => 2,
];
$workerManager = new PiCalculationWorkerManager($config);
$workerManager->run();
