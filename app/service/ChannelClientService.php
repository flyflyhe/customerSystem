<?php

namespace app\service;

use app\model\UserConnectionModel;
use Channel\Client as ChannelClient;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

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
        $toUid = $data['toUser']['uid'] ?? null;
        $fromUid = $data['fromUser']['uid'] ?? null;
        $message = $data['msg'];
        if ($toUid === null || $fromUid === null) {
            Worker::log("toUid 或 fromUid 未设置".json_encode($data));
            return;
        }

        $userConn = UserConnectionModel::getUidConnectionMap();

        $conn = $userConn[$toUid] ?? null;
        if ($conn instanceof TcpConnection) {
            $conn->send(json_encode(['event' => 'send', 'data' => ['msg' => $message, 'fromUser' => ['uid' => $fromUid, 'username' => '']]]));
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