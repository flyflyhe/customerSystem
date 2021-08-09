<?php

namespace app\service\db;

use app\component\MysqlConnection;

class MysqlService
{
    private static ?MysqlConnection $connection = null;

    public static function init($host, $port, $user, $password, $dbName)
    {
        self::$connection = new MysqlConnection($host, $port, $user, $password, $dbName);
    }

    public static function getDb():MysqlConnection
    {
        if (self::$connection === null) {
            throw new \Exception('db 连接未初始化');
        }
        return self::$connection;
    }
}