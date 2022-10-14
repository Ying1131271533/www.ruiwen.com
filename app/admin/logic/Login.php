<?php
namespace app\admin\logic;

use app\common\lib\exception\Error;
use app\common\model\User;
use think\facade\Cache;

class Login
{
    public static function getToken(array $data)
    {
        $scope  = $data['scope'];
        $id     = $data['id'];
        $values = [
            'scope' => $scope,
            'id'    => $id,
        ];
        $token      = self::saveToken($values);
        $updateData = [
            'id'        => $id,
            'last_ip'   => get_client_ip(),
            'last_time' => time(),
        ];
        User::update($updateData);
        return $token;
    }

    private static function saveToken($values)
    {
        $token  = self::generateToken();
        $expire = config('app.token_expire');
        $result = Cache::store('redis')->set($token, json_encode($values), $expire);
        if (!$result) {
            throw new Error(['msg' => '服务器缓存异常']);
        }
        return $token;
    }

    public static function generateToken()
    {
        $randChar  = get_rand_char(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('app.token_salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }

}
