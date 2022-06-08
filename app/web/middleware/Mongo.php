<?php

namespace app\web\middleware;

/**
 * 商品中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   \Closure $next
 */
class Mongo
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
