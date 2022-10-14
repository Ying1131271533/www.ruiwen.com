<?php
namespace app\web\logic;

use app\common\lib\exception\Error;
use app\common\lib\exception\Token as ExceptionToken;
use think\facade\Cache;

class Token
{
    private static $token = '';
    public static function getToken()
    {
        $token = self::generateToken();
        return $token;
    }

    /**
     * 生产token随机数
     */
    private static function generateToken()
    {
        $randChar  = get_rand_char(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('app.token_salt');
        $token     = md5($randChar . $timestamp . $tokenSalt);
        // 查找缓存中是否有同名的token，如果存在，则重新生成新的token名称
        $cache = Cache::store('redis')->get($token);
        if ($cache) {
            return self::generateToken();
        }
        return $token;
    }

    public static function saveToCache($token, $data, $expire = 0)
    {
        if (!$expire) {
            $expire = config('app.token_expire');
        }

        $result = Cache::store('redis')->set($token, json_encode($data), $expire);
        if (!$result) {
            throw new Error('服务器缓存异常');
        }
        return $result;
    }

    // 获取当前用户的user_id
    public static function getCurrentUserIdByToken($token){
        self::$token = $token;
        return self::getTokenVar('user_id');
    }

    // 获取当前用户的openid
    public static function getCurrentOpenidByToken($token){
        self::$token = $token;
        return self::getTokenVar('openid');
    }

    public static function getTokenVar(string $key)
    {
        $vars = Cache::store('redis')->get(self::$token);
        if (!$vars) {
            throw new ExceptionToken();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new ExceptionToken(['msg' => '权限查询失败']);
            }
        }
    }
}
