<?php

namespace app\service\web;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

interface WebHandle
{
    public function handle(TcpConnection $connection, Request $request, Response $response, array $argv);
}