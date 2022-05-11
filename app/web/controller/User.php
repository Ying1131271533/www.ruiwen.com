<?php
namespace app\web\controller;

use app\common\model\User as ModelUser;
use app\lib\exception\Fail;
use app\Request;

class User
{
    public function saveInfo(Request $requst)
    {
        $user_id = $requst->user_id;
        $params  = $requst->params;
        $data    = [
            'id'        => $user_id,
            'nick_name' => $params['nickName'],
            'gender'    => $params['gender'],
            'avatar'    => $params['avatarUrl'],
        ];
        $user   = ModelUser::find($user_id);
        $result = $user->save($data);
        if (!$result) {
            throw new Fail('登录失败');
        }
        return success();
    }
}
