<?php

require_once('autoload.php');

set_time_limit(-1);


$arguments = $argv;
array_shift($arguments);
if (count($arguments) == 0) {
    echo 'Wrong arguments';
    return;
}


$processClass = $arguments[0];
array_shift($arguments);

$messagingStrategy = new \ParallelLibrary\StreamMessagingStrategy(STDIN, STDOUT);
$process = new $processClass($messagingStrategy);
$process->run($arguments);
