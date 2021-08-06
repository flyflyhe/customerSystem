<?php

namespace app\service;

use Channel\Client as ChannelClient;

class ChannelClientService
{
    private static array $eventFuncMap = [
        ChannelEvent::EVENT_SEND_USER_TO_USER => 'sendUserToUser',
        ChannelEvent::EVENT_SEND_ALL => 'sendAll'
    ];

    public function __construct()
    {
    }

    public function sendUserToUser($data)
    {
        var_dump($data);
        echo 'send user to user', PHP_EOL;
    }

    public function sendAll($data)
    {
        echo 'send all', PHP_EOL;
    }

    public function start()
    {
        ChannelClient::connect();
        foreach (self::$eventFuncMap as $event => $func) {
            ChannelClient::on($event, [$this, 'sendUserToUser']);
        }
    }
}