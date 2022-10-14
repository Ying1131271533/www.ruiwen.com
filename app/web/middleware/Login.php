<?php

namespace app\web\middleware;

use app\common\model\User;
use app\common\lib\exception\Miss;

/**
 * 用户登录中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   Closure $next
 */
class Login
{
    public function handle($request, \Closure $next)
    {
        $params           = $request->params;
        $action           = $request->action();
        $user_id          = self::checkByAction($action, $params);
        $request->user_id = $user_id;
        return $next($request);
    }

    public static function checkByAction($action, $params)
    {
        $user_id = 0;
        switch ($action) {
            case 'loginAccount':
                $user = User::findUser($params['username'], md5($params['password']));
                if (!$user) {
                    throw new Miss('帐号或者密码错误');
                }
                $user_id = $user['id'];
                break;
        }

        if ($user_id == 0) {
            throw new Miss('帐号或者密码错误');
        }

        return $user_id;
    }

}
