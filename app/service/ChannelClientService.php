<?php

namespace app\service;

use app\model\UserConnectionModel;
use app\tool\Json;
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
            Worker::log("toUid 或 fromUid 未设置".json_encode($data, JSON_UNESCAPED_UNICODE));
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
        $fromUid = $data['fromUser']['uid'] ?? null;
        $fromUsername = $data['fromUser']['username'] ?? '';
        $msg = $data['msg'];
        if ($fromUid === null) {
            Worker::log("fromUid 未设置".json_encode($data, JSON_UNESCAPED_UNICODE));
            return;
        }
        $userConn = UserConnectionModel::getUidConnectionMap();

        foreach ($userConn as $uid => $conn) {
            if ($uid === $fromUid) {
                continue;
            }
            if ($conn instanceof TcpConnection) {
                $conn->send(Json::encode([
                    'event' => 'send',
                    'data' => ['msg' => $msg, 'fromUser' => ['uid' => $fromUid, 'username' => $fromUsername]]
                ]));
            }
        }
    }

    public function start()
    {
        ChannelClient::connect();
        foreach (self::$eventFuncMap as $event => $func) {
            ChannelClient::on($event, [$this, $func]);
        }
    }
}