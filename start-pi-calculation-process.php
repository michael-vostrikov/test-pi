<?php

set_time_limit(-1);

require_once('autoload.php');

$processClass = 'PiCalculationProcess';
$arguments = $argv;
array_shift($arguments);


$messagingStrategy = new \ParallelLibrary\StreamMessagingStrategy(STDIN, STDOUT);
$process = new $processClass($messagingStrategy);
$process->run($arguments);
