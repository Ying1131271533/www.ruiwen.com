<?php
declare (strict_types = 1);
namespace app\admin\logic;

use app\lib\exception\Forbidden;
use app\lib\exception\Token as TokenException;
use app\lib\exception\Unauthorized;
use think\facade\Cache;

class Token
{
    private static $token = '';
    public static function checkScope(string $token): int
    {
        self::$token = $token;
        $scope       = self::getTokenVar('scope');
        if ($scope) {
            if ($scope >= config('app.scope')) {
                return self::getTokenVar('id');
            } else {
                throw new Forbidden();
            }
        } else {
            throw new Unauthorized();
        }
    }

    public static function getTokenVar(string $key)
    {
        $vars = Cache::store('redis')->get(self::$token);
        // $vars = Cache::store('redis')->get(self::$token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new TokenException(['msg' => '权限查询失败']);
            }
        }
    }
}
