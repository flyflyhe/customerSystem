<?php

namespace app\model;

use app\component\MysqlConnection;
use app\service\db\MysqlService;
use app\tool\Json;

abstract class BaseModel
{
    abstract static function getTable():string;

    abstract public function attributes():array;

    public function primaryKey():string
    {
        return 'id';
    }

    public static function find():MysqlConnection
    {
        return MysqlService::getDb()->table(static::getTable());
    }

    public function save():bool
    {
        $param = $this->toArray();
        $p = $this->primaryKey();
        unset($param[$p]);
        $id = MysqlService::getDb()->insert(static::getTable())->cols($this->toArray())->query();
        if ($id > 0) {
            $this->{$p} = $id;
            return true;
        }
        return false;
    }

    public function update()
    {
        $p = $this->primaryKey();
        $param = $this->toArray();
        unset($param[$p]);
        return MysqlService::getDb()->update(static::getTable())->cols($this->toArray())->where([$p => $this->{$p}])->query();
    }

    public function toArray():array
    {
        $result = [];
        foreach ($this->attributes() as $k) {
            $result[$k] = $this->$k;
        }
        return $result;
    }
}