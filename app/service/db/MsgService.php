<?php

namespace app\service\db;

use app\model\MsgModel;
use app\service\SingleInstanceService;

class MsgService extends SingleInstanceService
{
    public function add(int $uid, string $content, int $toUid = 0, int $groupId = 0):bool
    {
        $msg = new MsgModel();
        $msg->uid = $uid;
        $msg->to_uid = $toUid;
        $msg->group_id = $groupId;
        $msg->content = $content;
        $msg->created = $msg->updated = date('Y-m-d H:i:s');

        return $msg->save();
    }

    public function getListByUidAndToUid(int $uid, int $toUid):array
    {
        return MsgModel::find()->where("uid = :uid and to_uid = :toUid")->bindValues(['uid' => $uid, 'to_uid' => $toUid])->all();
    }
}