<?php

use app\service\ChannelClientService;
use Workerman\Worker;
use Workerman\Protocols\Text;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;

require dirname(__DIR__).'/vendor/autoload.php';

$worker = new Worker('tcp://0.0.0.0:9999');
$worker->protocol = Text::class;
// 4 processes
$worker->count = 4;

// Emitted when new connection come
$worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

$worker->onWorkerStart = function ($task) {
    $channelClientService = new ChannelClientService();
    $channelClientService->start();

    Timer::add(2, function () {
        \Channel\Client::publish(\app\service\ChannelEvent::EVENT_SEND_USER_TO_USER, ['hi']);
    });
};

// Emitted when data received
$worker->onMessage = function (TcpConnection $connection, $data) {
    //echo $data, PHP_EOL;
    // Send data to client
    echo $connection->worker->workerId, PHP_EOL;
    $connection->send("Hello $data \n");
};

// Emitted when connection is closed
$worker->onClose = function ($connection) {
    echo "Connection closed\n";
};

Worker::runAll();