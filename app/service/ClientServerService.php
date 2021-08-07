<?php

namespace app\service;

use app\model\UserConnectionModel;
use Channel\Client as ChannelClient;
use Workerman\Connection\ConnectionInterface;

class ClientServerService
{
    private static array $eventHandleMap = [
        ClientServerEvent::EVENT_LOGIN => 'login',
        ClientServerEvent::EVENT_LOGOUT => 'logout',
        ClientServerEvent::EVENT_SEND => 'send',
    ];

    private string $event;

    private array $data;

    private ConnectionInterface $conn;

    public function __construct(string $event, array $data, ConnectionInterface $connection)
    {
        $this->event = $event;
        $this->data = $data;
        $this->conn = $connection;
        var_dump($this->data);
    }

    public function login(self $obj)
    {
        $userConnectionModel = new UserConnectionModel();
        $userConnectionModel->add($this->data['uid'], $this->conn);
    }

    public function logout()
    {
        $userConnectionModel = new UserConnectionModel();
        $userConnectionModel->removeByUid($this->data['uid']);
    }

    public function send()
    {
        ChannelClient::publish(ChannelEvent::EVENT_SEND_USER_TO_USER, [
            'toUser' => ['uid' => (int)$this->data['toUid'], 'username' => ''],
            'fromUser' => ['uid' => (int)$this->data['fromUid'], 'username' => ''],
            'msg' => $this->data['msg'],
        ]);
    }

    public function handle()
    {
        if (!isset(self::$eventHandleMap[$this->event])) {
            throw new \Exception($this->event.'未注册');
        }
        return call_user_func([$this, self::$eventHandleMap[$this->event]], $this);
    }
}