<?php

namespace app\model;

use Workerman\Connection\ConnectionInterface;

class UserConnectionModel
{
    private static array $uidConnectionMap = [];

    private static array $uidInfo = [];

    /**
     * @return array
     */
    public static function getUidConnectionMap(): array
    {
        return self::$uidConnectionMap;
    }

    public static function getAdminUidConnectionMap(): array
    {
        $result = [];
        foreach (self::$uidInfo as $uid => $info) {
            if ($info['is_admin']) {
                $result[$uid] = self::$uidConnectionMap[$uid];
            }
        }

        return $result;
    }

    public static function getClientUidConnectionMap(): array
    {
        $result = [];
        foreach (self::$uidInfo as $uid => $info) {
            if (empty($info['is_admin'])) {
                $result[$uid] = self::$uidConnectionMap[$uid];
            }
        }

        return $result;
    }

    public function add(int $uid, ConnectionInterface $connection, array $userInfo)
    {
        self::$uidConnectionMap[$uid]= $connection;
        self::$uidInfo[$uid] = $userInfo;
    }

    public function removeByUid(int $uid)
    {
        if (isset(self::$uidConnectionMap[$uid])) {
            unset(self::$uidConnectionMap[$uid]);
        }
        if (isset(self::$uidInfo[$uid])) {
            unset(self::$uidInfo[$uid]);
        }
    }

    public function removeByConnection(ConnectionInterface $connection)
    {
        foreach (self::$uidConnectionMap as $uid => $conn) {
            if ($conn === $connection) {
                $this->removeByUid($uid);
            }
        }
    }
}