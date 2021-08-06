<?php

namespace app\service;

use Workerman\Connection\TcpConnection;

interface Handle
{
    public function handle(TcpConnection $connection, $data);
}