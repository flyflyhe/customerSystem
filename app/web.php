<?php

use app\model\UserConnectionModel;
use app\service\db\MysqlService;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Request;
use Workerman\Worker;

require dirname(__DIR__).'/vendor/autoload.php';
require 'bootstrap/bootstrap.php';

$worker = new Worker('http://0.0.0.0:9001');
// 4 processes
$worker->count = 4;

// Emitted when new connection come
$worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

$worker->onWorkerStart = function ($task) {
    MysqlService::init($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
};

// Emitted when data received
$worker->onMessage = function (ConnectionInterface $connection, Request $request) {
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', 'get_all_users_handler');
        // {id} must be a number (\d+)
        $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
        // The /{title} suffix is optional
        $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    });

    // Fetch method and URI from somewhere
    $httpMethod = $request->method();
    $uri = $request->uri();

    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            // ... call $handler with $vars
            break;
    }
};

// Emitted when connection is closed
$worker->onClose = function ($connection) {
    $userConnectModel = new UserConnectionModel();
    $userConnectModel->removeByConnection($connection);
    echo "Connection closed\n";
};

Worker::runAll();