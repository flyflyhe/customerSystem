<?php

namespace app\service;

use app\service\ChannelEvent;

class SingleInstanceService
{
    private static array $instanceMap = [];

    public static function getInstance():static
    {
        $class = get_called_class();
        if (!isset(self::$instanceMap[$class])) {
            self::$instanceMap[$class] = new static();
        }

        return self::$instanceMap[$class];
    }
}