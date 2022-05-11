<?php
declare (strict_types = 1);
namespace app\common\controller;

use app\lib\exception\Fail;
use app\lib\exception\Success;
use lib\Crypt as C;


class Crypt
{
    /**
     * 获取密钥和签名
     *
     * @return Response    api返回的json数据
     */
    public function get_key()
    {
        // 获取密钥
        $key = C::web_key();
        if (empty($key)) {
            throw new Fail(['msg' => '密钥获取失败']);
        }

        // 获取签名
        $sign = C::sign(config('app.sign'), $key['publickey']);
        if (empty($sign)) {
            throw new Fail(['msg' => '签名获取失败']);
        }
        $key['sign'] = $sign;

        return success($key);
    }
}
