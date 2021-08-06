<?php

namespace app\model;

use Workerman\Connection\ConnectionInterface;

class UserConnectionModel
{
    private static array $uidConnectionMap = [];

    /**
     * @return array
     */
    public static function getUidConnectionMap(): array
    {
        return self::$uidConnectionMap;
    }

    public function add(int $uid, ConnectionInterface $connection)
    {
        self::$uidConnectionMap[$uid] = $connection;
    }

    public function removeByUid(int $uid)
    {
        if (isset(self::$uidConnectionMap[$uid])) {
            unset(self::$uidConnectionMap[$uid]);
        }
    }

    public function removeByConnection(ConnectionInterface $connection)
    {
        foreach (self::$uidConnectionMap as $uid => $conn) {
            if ($conn === $connection) {
                unset(self::$uidConnectionMap[$uid]);
            }
        }
    }

}