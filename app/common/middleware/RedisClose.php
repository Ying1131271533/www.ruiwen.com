<?php
namespace app\common\middleware;

use think\cache\driver\Redis;

/**
 * 关闭Redis
 *
 */
class RedisClose
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        // 实例化Redis
        $redis = new Redis();
        // 关闭redis
        $redis -> close();
        return $response;
    }
}
