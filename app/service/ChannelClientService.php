<?php

namespace app\service;

use app\model\UserConnectionModel;
use Channel\Client as ChannelClient;
use Workerman\Connection\TcpConnection;

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
        $toUid = $data['toUid'];
        $userConn = UserConnectionModel::getUidConnectionMap();

        $conn = $userConn[$toUid] ?? null;
        if ($conn instanceof TcpConnection) {
            $conn->send(json_encode(['event' => 'send', 'msg' => 'hi']));
        }
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