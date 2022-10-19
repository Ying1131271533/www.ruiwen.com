<?php
namespace app\common\middleware;

use app\common\lib\exception\Fail;

/**
 * 表单验证
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class Token
{
    public function handle($request, \Closure $next)
    {
        // 表单令牌验证
        $check = $request->checkToken('__token__');
        if($check === false) {
            throw new Fail(['msg' => '不能重复提交表单', 401, 10031]);
        }
        return $next($request);
    }
}
