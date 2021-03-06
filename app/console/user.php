<?php

use app\service\db\MysqlService;

require_once 'main.php';

MysqlService::init($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

$user = new \app\model\UserModel();
$user->name = 'admin';
$user->password = '';
$user->role = 1;
$user->created = $user->updated = date('Y-m-d H:i:s');
var_dump($user->save());
var_dump($user);

//$user = \app\model\UserModel::find()->where('id = :id')->bindValues(['id'=>1])->one();
//var_dump($user);
//
//$msgList = \app\model\MsgModel::find()->all();
//var_dump($msgList);