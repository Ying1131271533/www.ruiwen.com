<?php

namespace app\common\lib;

use app\common\logic\lib\Redis;

class Token
{
    public static function getToken()
    {
        return request()->header('access-token');
    }

    public static function getUser()
    {
        return (new Redis)->get(config('redis.token_pre') . self::getToken());
    }

    public static function getUserId()
    {
        return self::getUser()['id'];
    }
}
