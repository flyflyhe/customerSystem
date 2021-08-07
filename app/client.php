<?php

use Workerman\Connection\AsyncTcpConnection;
use Workerman\Timer;
use Workerman\Worker;
use Workerman\Protocols\Text;

require dirname(__DIR__).'/vendor/autoload.php';

$worker = new Worker();
$worker->onWorkerStart = function () {
    // Websocket protocol for client.

    $l = 2000;
    while ($l-- > 0) {
        $tcpConnection = new AsyncTcpConnection('tcp://0.0.0.0:9999');
        $tcpConnection->protocol = Text::class;
        $tcpConnection->onConnect = function ($connection) {
            $connection->send('mrHe');
        };
        $tcpConnection->onMessage = function ($connection, $data) {
            echo "Recv: $data\n";
        };
        $tcpConnection->onError = function ($connection, $code, $msg) {
            echo "Error: $msg\n";
        };
        $tcpConnection->onClose = function ($connection) {
            echo "Connection closed\n";
        };

        $tcpConnection->connect();

        $i = 0;
        $time_interval = 2.5;
        $timer_id = Timer::add($time_interval, function () use ($tcpConnection, &$i) {
            $tcpConnection->send($i++);
        });
    }

};