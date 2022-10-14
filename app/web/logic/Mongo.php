<?php
namespace app\web\logic;

use app\common\mongo\User;
use app\common\lib\exception\Miss;

class Mongo
{
    // 查找用户
    public static function findUserById($id)
    {
        $user = User::with('info')
            ->withCache(cache_time('one_day'))
            ->field('id, name, age')->where('id', $id)
            ->cache(cache_time())
            ->find();
        if (!$user) {
            throw new Miss('找不到此用户');
        }
        return $user;
    }
}
