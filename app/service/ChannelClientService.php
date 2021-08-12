<?php

namespace app\service;

use app\model\UserConnectionModel;
use app\model\UserModel;
use app\service\db\MsgService;
use app\tool\Json;
use Channel\Client as ChannelClient;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class ChannelClientService
{
    private static array $eventFuncMap = [
        ChannelEvent::EVENT_SEND_USER_TO_USER => 'sendUserToUser',
        ChannelEvent::EVENT_SEND_ALL => 'sendAll',
        ChannelEvent::EVENT_LOGIN_NOTIFY => 'notifyLogin',
        ChannelEvent::EVENT_LOGIN => 'login',
    ];

    public function __construct()
    {
    }

    public function sendUserToUser($data)
    {
        $toUid = $data['toUser']['uid'] ?? null;
        $fromUid = $data['fromUser']['uid'] ?? null;
        if ($toUid === null || $fromUid === null) {
            Worker::log("toUid 或 fromUid 未设置".json_encode($data, JSON_UNESCAPED_UNICODE));
            return;
        }

        MsgService::getInstance()->add($fromUid, $data['msg'], $toUid);

        $userConn = UserConnectionModel::getUidConnectionMap();

        $conn = $userConn[$toUid] ?? null;
        if ($conn instanceof TcpConnection) {
            $conn->send(json_encode(['event' => 'message', 'data' => $data]));
        }
    }

    public function sendAll($data)
    {
        $fromUid = $data['fromUser']['uid'] ?? null;
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
                    'data' => $data
                ]));
            }
        }
    }

    public function notifyLogin($data)
    {
        $fromUid = $data['user']['uid'] ?? null;
        $userConn = UserConnectionModel::getUidConnectionMap();

        foreach ($userConn as $uid => $conn) {
            if ($fromUid === $uid) {
                continue;
            }

            if ($conn instanceof TcpConnection) {
                $conn->send(Json::encode([
                    'event' => 'login_notify',
                    'data' => $data
                ]));
            }
        }
    }

    public function login($data)
    {
        $toUid = $data['user']['uid'] ?? null;
        $userConn = UserConnectionModel::getUidConnectionMap();

        $result = [];
        $conn = $userConn[$toUid] ?? null;
        if ($conn instanceof TcpConnection) {
            foreach ($userConn as $uid => $conn) {
                if ($toUid === $uid) {
                    continue;
                }

                $user = UserModel::getUserByUid($uid);
                if (!$user) {
                    continue;
                }

                $result[] = [
                    "area" => "北京-北京",
                    "autograph" => "不是每个人都能成为自己想要的样子，但每个人，都可以努力成为自己想要的样子.",
                    "avatar" => "http://www.lmsail.com/storage/9d770a4b695cc49ed23525bebca15790.jpeg",
                    "id" => $user->id,
                    "introduction" => "90后 | Mr.bo | PHPER工程师",
                    "lockstate" => 0,
                    "nickname" => $user->name,
                    "phone" => 18899888899
                ];
                var_dump($result);
            }


            $conn->send(Json::encode([
                'event' => 'login',
                'data' => $result
            ]));

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

