<?php

namespace app\service\web;

use app\model\UserModel;
use app\tool\Json;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

class LoginHandle implements WebHandle
{
    public function handle(TcpConnection $connection, Request $request, Response $response ,array $argv = [])
    {
        $username = $request->post('username');
        $password = $request->post('password');

        //验证省略
        $user = UserModel::find()->where("name = :name")->bindValues(['name' => $username])->one();

        if (!$user) {
            $response->withStatus(401);
            return $connection->send($response);
        }

        $response->withBody(Json::encode($user));

        return $connection->send($response);
    }
}