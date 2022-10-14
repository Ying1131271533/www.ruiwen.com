<?php
namespace app\web\middleware;

use app\common\lib\exception\Unauthorized;
use app\web\logic\Token as TokenLogic;

/**
 * 令牌校验
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class Auth
{
    public function handle($request, \Closure $next)
    {
        $token  = $request->header('token');
        if (!$token) {
            throw new Unauthorized('token不能为空');
        }
        
        $user_id = TokenLogic::getCurrentUserIdByToken($token);
        $request->user_id = $user_id;
        return $next($request);
    }
}
