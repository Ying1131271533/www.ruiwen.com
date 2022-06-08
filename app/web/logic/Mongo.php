<?php
namespace app\web\logic;

use app\common\mongo\User;
use app\lib\exception\Params;

class Mongo
{
    // 查找用户
    public static function findUserById($id)
    {
        if (!$id) {
            throw new Params('找不到此用户');
        }

        $user = User::with('info')->field('id, name, age')->where('id', $id)->find();
        return $user;
    }
}
