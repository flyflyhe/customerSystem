<?php

namespace app\component;

use Workerman\MySQL\Connection;

class MysqlConnection extends Connection
{
    protected string $modelClass = '';

    public function getPdo():\PDO
    {
        return $this->pdo;
    }

    public function tableExist(string $table)
    {
    }

    public function setModelClass($class):self
    {
        $this->modelClass = $class;
        return $this;
    }

    public function all($class = ''):array
    {
        if (!$class) {
            $class = $this->modelClass;
        }
        $result = [];
        $data = $this->query();
        if (is_array($data) && !empty($data) && class_exists($class)) {
            foreach ($data as $row) {
                $result[] = $this->buildModel($row, $class);
            }
        }

        return $result;
    }

    public function one($class = '')
    {
        if (!$class) {
            $class = $this->modelClass;
        }
        $data = $this->query();
        if (is_array($data) && !empty($data) && class_exists($class)) {
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
                            $property->setValue($obj, (string)$v);
                            break;
                        case 'integer':
                        case 'int':
                            $property->setValue($obj, (int)$v);
                            break;
                        default:;
                    }
                } else {
                    $property->setValue($obj, $v);
                }
            }
        }

        return $obj;
    }
}