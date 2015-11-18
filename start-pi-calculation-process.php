<?php

set_time_limit(-1);

require_once('autoload.php');

$processClass = 'PiCalculationProcess';
$arguments = $argv;
array_shift($arguments);

$process = new $processClass();
$process->run($arguments);
