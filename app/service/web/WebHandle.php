<?php

namespace app\service\web;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

interface WebHandle
{
    public function handle(TcpConnection $connection, Request $request, array $argv);
}