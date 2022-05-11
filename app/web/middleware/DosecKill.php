<?php

namespace app\web\middleware;

use app\common\model\Product;
use app\common\model\User;
use app\lib\exception\Miss;
use think\exception\ValidateException;

/**
 * 秒杀中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   \Closure $next
 */
class DosecKill
{
    public function handle($request, \Closure $next)
    {
        // halt($request->params);
        /* // 获取参数
        $params = $request->params;

        // 找到用户
        $user = User::find($params['user_id']);
        if(!$user) throw new Miss('该用户id不存在');

        // 找到商品
        $product = Product::find($params['product_id']);
        if(!$product) throw new Miss('该商品id不存在');

        $request->user = $user;
        $request->product = $product; */
        return $next($request);
    }
}
