<?php

namespace app\web\middleware;

use app\common\validate\IdMusetBePositiveInt;
use app\lib\exception\Params;
use think\exception\ValidateException;

/**
 * 商品中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   \Closure $next
 */
class Product
{
    public function handle($request, \Closure $next)
    {
        // UP主添加的一个验证id为正整数的规则，感觉没什么用
        /* $params = $request->params;
        try {
            validate(IdMusetBePositiveInt::class)->check($params);
        } catch (ValidateException $e) {
            throw new Params($e->getError());
        }

        $id = $params['id'];
        $request->id=$id; */
        return $next($request);
    }
}
