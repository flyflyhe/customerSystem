<?php

namespace app\tool;

class Json
{
    public static function encode($data):string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function decode($data):array
    {
        return json_decode($data, true);
    }
}