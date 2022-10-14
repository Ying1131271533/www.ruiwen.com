<?php
namespace app\web\middleware;

use app\common\model\ProductSpecs;
use app\common\lib\exception\Miss;

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/02/28 16:44
 *
 * 购物车中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class Cart
{
    public function handle($request, \Closure $next)
    {
        $params = $request->params;
        $action = $request->action();
        if ($action == 'index') {
            // 对增、删、改进行校验
            self::checkParams($params, $action);
        }

        return $next($request);
    }

    public static function checkParams($params, $action)
    {
        if ($action == 'save') {
            $spec = ProductSpecs::find($params['sepc_id']);
            // halt($spec);
            if(!$spec){
                throw new Miss('商品规格不存在');
            }
        }
    }
}
