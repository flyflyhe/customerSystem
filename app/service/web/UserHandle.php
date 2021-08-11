<?php

namespace app\service\web;

use app\model\UserModel;
use app\tool\Json;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

class UserHandle implements WebHandle
{
    public function handle(TcpConnection $connection, Request $request, Response $response, array $argv = [])
    {
        $id = (int)$request->post('token');

        //验证省略
        $user = UserModel::find()->where("id = :id")->bindValues(['id' => $id])->one();

        if (!$user instanceof UserModel) {
            $response->withStatus(401);
            return $connection->send($response);
        }

        $result = [
            "area" => "北京-北京",
            "autograph" => "不是每个人都能成为自己想要的样子，但每个人，都可以努力成为自己想要的样子.",
            "avatar" => "http://www.lmsail.com/storage/9d770a4b695cc49ed23525bebca15790.jpeg",
            "id" => $user->id,
            "introduction" => "90后 | Mr.bo | PHPER工程师",
            "lockstate" => 0,
            "nickname" => $user->name,
            "phone" => 18899888899
        ];
        $response->withBody(Json::encode($result));

        return $connection->send($response);
    }
}