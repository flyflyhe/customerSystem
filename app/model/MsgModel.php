<?php

namespace app\model;

class MsgModel extends BaseModel
{
    public int $id = 0;

    public int $uid;

    public int $to_uid;

    public int $group_id;

    public string $content;

    public string $created;

    public string $updated;

    static function getTable(): string
    {
        return 'msg';
    }

    static public function attributes(): array
    {
        return ['id', 'uid', 'to_uid', 'group_id', 'content', 'created', 'updated'];
    }
}