<?php
namespace app\admin\middleware;

use app\common\model\Admin;
use app\lib\exception\Miss;

/**
 * 管理员登录中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class Login
{
    public function handle($request, \Closure $next)
    {
        $params = $request->params;
        $admin   = Admin::findUser($params['username'], $params['password']);
        if (empty($admin)) {
            throw new Miss('账号或者密码错误');
        }
        // 返回管理员数据
        $request->admin = $admin->toArray();
        return $next($request);
    }
}
