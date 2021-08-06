<?php

use Workerman\Worker;

require dirname(__DIR__) . '/vendor/autoload.php';

$channel = new Channel\Server();

Worker::runAll();