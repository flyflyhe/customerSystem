<?php

namespace app\model;

use app\service\db\MysqlService;

class UserModel extends BaseModel
{
    public ?int $id = 0;

    public string $name;

    public ?string $password;

    public int $role;

    public ?string $created;

    public ?string $updated;

    public static function getTable():string
    {
        return 'user';
    }

    public static function attributes(): array
    {
        return ['id', 'name', 'password', 'role', 'created', 'updated'];
    }
}