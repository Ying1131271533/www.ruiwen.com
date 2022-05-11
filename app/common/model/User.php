<?php

namespace app\common\model;

class User extends BaseModel
{
    public static function findUser($username, $password)
    {
        $user = self::where(['username' => $username, 'password' => $password])->find();
        return $user;
    }
}
