<?php
namespace app\admin\controller;

use app\common\lib\exception\Fail;
use app\common\lib\exception\Forbidden;
use app\common\lib\exception\Miss;
use app\common\model\Admin as A;
use lib\Crypt;
use app\Request;

class Admin
{
    public function info(Request $request)
    {
        $user_id = $request->user_id;
        return success($user_id);
    }

    public function akali(Request $request)
    {
        // 接收参数
        $params = $request->all();

        // 验签
        $signResult = Crypt::verify(config('app.sign'), $params['sign'], $params['publickey']);
        if (empty($signResult)) {
            throw new Forbidden(['msg' => '验签不通过']);
        }

        // 获取解密数据
        $cryptData = Crypt::decrypt($params);
        if (empty($signResult)) {
            throw new Forbidden(['msg' => '数据解密失败！']);
        }

        // 找出该用户
        $user = A::field('id, username, phone')->where('phone|email', $cryptData['username'])->find();
        if (empty($user)) {
            throw new Miss();
        }

        // 获取加密数据返回用户端
        $resultData = Crypt::encrypt($user->toArray(), $params['web_privatekey']);
        if (empty($signResult)) {
            throw new Fail(['msg' => '数据加密失败！']);
        }

        return success($resultData);
    }
}
