<?php

namespace app\service;

use app\model\UserConnectionModel;
use app\service\handle\AddUserHandle;
use app\service\handle\Handle;
use Channel\Client as ChannelClient;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

class ClientServerService
{
    private static array $eventHandleMap = [
        ClientServerEvent::EVENT_LOGIN => 'login',
        ClientServerEvent::EVENT_LOGOUT => 'logout',
        ClientServerEvent::EVENT_SEND => 'send',
        ClientServerEvent::EVENT_SEND_ALL => 'sendAll',
        ClientServerEvent::EVENT_ADD_USER => AddUserHandle::class,
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

    public static function getEventHandleMap(): array
    {
        return self::$eventHandleMap;
    }

    //"{"event":"login","data":{"user":{"uid":1,"role":1,"username":"abc"}}}"
    public function login(self $obj)
    {
        $userConnectionModel = new UserConnectionModel();
        $userConnectionModel->add($this->data['user']['uid'], $this->conn, $this->data['user']);
        ChannelClient::publish(ChannelEvent::EVENT_LOGIN_NOTIFY, [
            'user' => ['uid' => $this->data['user']['uid'], 'username' => $this->data['user']['username'] ?? ''],
            'msg' => ($this->data['user']['username'] ?? '').'上线',
        ]);
        ChannelClient::publish(ChannelEvent::EVENT_LOGIN, [
            'user' => ['uid' => $this->data['user']['uid']],
        ]);
    }

    //"{"event":"login","data":{"user":{"uid":1,"role":1}}}"
    public function logout()
    {
        $userConnectionModel = new UserConnectionModel();
        $userConnectionModel->removeByUid($this->data['user']['uid']);
    }

    //{"event":"message","data":{"fromUser":{"uid":1,"username":"11"},"toUser":{"uid":2,"username":"222"},"msg":"you are a dog"}}
    public function send()
    {
        ChannelClient::publish(ChannelEvent::EVENT_SEND_USER_TO_USER, [
            'toUser' => ['uid' => (int)$this->data['toUser']['uid'], 'username' => $this->data['toUser']['username']],
            'fromUser' => ['uid' => (int)$this->data['fromUser']['uid'], 'username' => $this->data['fromUser']['username']],
            'msg' => $this->data['msg'],
        ]);
    }

    public function sendAll()
    {
        ChannelClient::publish(ChannelEvent::EVENT_SEND_ALL, [
            'fromUser' => ['uid' => (int)$this->data['fromUser']['uid'], 'username' => $this->data['fromUser']['username']],
            'msg' => $this->data['msg'],
        ]);
    }

    public function handle()
    {
        if (!isset(self::$eventHandleMap[$this->event])) {
            Worker::log("未注册的事件".$this->event);
            return false;
        }
        $handle = self::$eventHandleMap[$this->event];
        if (is_callable($handle)) {
            return call_user_func($handle, $this);
        } elseif (is_callable([$this, $handle])) {
            return call_user_func([$this, $handle], $this);
        } elseif (class_exists($handle) && class_implements($handle, Handle::class)) {
            $handleObj = new $handle();
            if ($handleObj instanceof Handle) {
                return $handleObj->handle($this);
            }
        }

        Worker::log("{$handle} 不可用");
        return false;
    }
}