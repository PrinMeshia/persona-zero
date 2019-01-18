<?php

use Bin\Config\Console;

require  __DIR__ . '/../vendor/autoload.php';

if (count($argv) == 1) {
    die("Worksena Console - version: 0.0.1".PHP_EOL);
}

if ($argv[1] == "server:run") {
    echo 'Server started on '.Console::SERVER_HOST.':'.Console::SERVER_PORT.' Executed the: '.(new \DateTime())->format('Y/m/d H:i:s').PHP_EOL;
    $command = '"'.PHP_BINARY.'"';
    $command .= ' -S '.Console::SERVER_HOST.':'.Console::SERVER_PORT;
    passthru($command);
}