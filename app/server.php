<?php

use app\model\UserConnectionModel;
use app\service\ChannelClientService;
use app\service\ClientServerService;
use app\service\db\MysqlService;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

require dirname(__DIR__).'/vendor/autoload.php';
require 'bootstrap/bootstrap.php';

$worker = new Worker('websocket://0.0.0.0:9000');
// 4 processes
$worker->count = 4;

// Emitted when new connection come
$worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

$worker->onWorkerStart = function ($task) {
    $channelClientService = new ChannelClientService();
    $channelClientService->start();

    MysqlService::init($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

    foreach (ClientServerService::getEventHandleMap() as $event => $_) {
        echo "注册事件", $event, PHP_EOL;
    }
};

// Emitted when data received
$worker->onMessage = function (ConnectionInterface $connection, $data) {
    $map = json_decode($data, true);
    if (is_array($map) && isset($map['event']) && isset($map['data'])) {
        $clientServerService = new ClientServerService($map['event'], $map['data'], $connection);
        $clientServerService->handle();
    } else {
        echo '无效数据', PHP_EOL;
        var_dump($data);
    }
};

// Emitted when connection is closed
$worker->onClose = function ($connection) {
    $userConnectModel = new UserConnectionModel();
    $userConnectModel->removeByConnection($connection);
    echo "Connection closed\n";
};

Worker::runAll();