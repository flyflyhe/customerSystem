<?php

use Workerman\Worker;
use Workerman\Protocols\Text;

require dirname(__DIR__).'/vendor/autoload.php';

$tcpWorker = new Worker('tcp://0.0.0.0:9999');
$tcpWorker->protocol = Text::class;
// 4 processes
$tcpWorker->count = 1;

// Emitted when new connection come
$tcpWorker->onConnect = function ($connection) {
    echo "New Connection\n";
};

// Emitted when data received
$tcpWorker->onMessage = function ($connection, $data) {
    echo $data, PHP_EOL;
    // Send data to client
    $connection->send("Hello $data \n");
};

// Emitted when connection is closed
$tcpWorker->onClose = function ($connection) {
    echo "Connection closed\n";
};

Worker::runAll();