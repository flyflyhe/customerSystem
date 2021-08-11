<?php

use app\service\db\MysqlService;
use app\service\web\LoginHandle;
use app\service\web\UserHandle;
use app\service\web\WebHandle;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;

require dirname(__DIR__).'/vendor/autoload.php';
require 'bootstrap/bootstrap.php';

$worker = new Worker('http://0.0.0.0:9001');

// Emitted when new connection come
$worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

$worker->onWorkerStart = function ($task) {
    MysqlService::init($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
};

// Emitted when data received
$worker->onMessage = function (TcpConnection $connection, Request $request) {
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('POST', '/login', LoginHandle::class);
        $r->addRoute('POST', '/user', UserHandle::class);
        // {id} must be a number (\d+)
        //$r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
        // The /{title} suffix is optional
        //$r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    });;

    $httpMethod = $request->method();
    $response = new Response();
    $response->withHeaders([
        'Access-Control-Allow-Origin' => '*', //'http://127.0.0.1:3000',
        'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE,OPTIONS,HEAD',
        'Access-Control-Allow-Credentials' => true,
        'Access-Control-Max-Age' => 86400,
        'Access-Control-Allow-Headers' => 'Content-Type,Authorization,X-Requested-With,Accept,Origin, xapi',
    ]);
    if ($httpMethod === 'OPTIONS') {
        $connection->send($response);
        return;
    }

    $uri = $request->uri();

    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);

    echo $httpMethod, '||', $uri, PHP_EOL;

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $connection->send((new Response())->withStatus(404));
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $classOrFunc = $routeInfo[1];
            $args = (array)$routeInfo[2];
            var_dump($classOrFunc);
            if (is_string($classOrFunc) && class_exists($classOrFunc)) {
                $handler = new $classOrFunc();
                if ($handler instanceof WebHandle) {
                    $handler->handle($connection, $request, $response, $args);
                }
            }
            break;
    }
};

// Emitted when connection is closed
$worker->onClose = function ($connection) {
    echo "Connection closed\n";
};

Worker::runAll();