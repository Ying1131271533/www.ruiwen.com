<?php
namespace app\web\logic;

use app\common\mongo\User;
use app\lib\exception\Miss;

class Mongo
{
    // 查找用户
    public static function findUserById($id)
    {
        $user = User::with('info')->field('id, name, age')->where('id', $id)->find();
        if (!$user) {
            throw new Miss('找不到此用户');
        }
        return $user;
    }
}
