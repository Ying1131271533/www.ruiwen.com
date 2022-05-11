<?php

namespace app\common\model;

class Admin extends BaseModel
{
    public static function findUser($username, $password)
    {
        $user = self::where(['username' => $username, 'password' => $password])->find();
        return $user;
    }
}
