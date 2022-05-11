<?php

namespace app\admin\middleware;

/**
 * 商品规格名称中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class ProductSpecsName
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
