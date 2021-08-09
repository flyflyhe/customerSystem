<?php

namespace app\component;

use Workerman\MySQL\Connection;

class MysqlConnection extends Connection
{
    public function getPdo():\PDO
    {
        return $this->pdo;
    }

    public function tableExist(string $table)
    {
    }

    public function all($class = ''):array
    {
        $result = [];
        $data = $this->query();
        if (is_array($data) && class_exists($class)) {
            foreach ($data as $row) {
                $result[] = $this->buildModel($row, $class);
            }
        }

        return $result;
    }

    public function one($class = '')
    {
        $data = $this->query();
        if (is_array($data) && class_exists($class)) {
            return $this->buildModel($data[0], $class);
        }

        return $data;
    }

    public function buildModel($result, $class)
    {
        $obj = new $class();
        $reflectObj = new \ReflectionClass($obj);
        foreach ($result as $k => $v) {
            if ($reflectObj->hasProperty($k)) {
                $property = new \ReflectionProperty($obj, $k);
                if ($property->hasType()) {
                    $typeName = $property->getType()->getName();
                    switch ($typeName) {
                        case 'string':
                            $property->setValue((string)$v);
                            break;
                        case 'integer':
                        case 'int':
                            $property->setValue((int)$v);
                            break;
                        default:;
                    }
                } else {
                    $property->setValue($v);
                }
            }
        }

        return $obj;
    }
}