<?php

namespace app\service\db;

use app\service\handle\SingleInstanceService;
use Workerman\MySQL\Connection;

class MysqlService
{
    private static ?Connection $connection = null;

    public static function init($host, $port, $user, $password, $dbName)
    {
        self::$connection = new Connection($host, $port, $user, $password, $dbName);
    }

    public static function getDb():Connection
    {
        if (self::$connection === null) {
            throw new \Exception('db 连接未初始化');
        }
        return self::$connection;
    }
}