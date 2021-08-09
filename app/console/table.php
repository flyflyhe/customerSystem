<?php

use app\service\db\MysqlService;

require_once 'main.php';

MysqlService::init($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

$sqlContent = file_get_contents(__DIR__.'/table.sql');

$sqlList = explode(';', $sqlContent);

foreach ($sqlList as $sql) {
    echo $sql, PHP_EOL;
    try {
        MysqlService::getDb()->getPdo()->exec($sql);
    } catch (\Throwable $e) {
        echo $e->getCode(), $e->getMessage(), PHP_EOL;
        if ($e->getCode() === '42S02') {
            continue;
        }
    }
}