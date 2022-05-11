<?php
namespace app\common\middleware;

use think\cache\driver\Memcache;

/**
 * 关闭Memcache
 *
 */
class MemcacheClose
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        // 实例化Memcache
        $redis = new Memcache();
        // 关闭Memcache
        $redis -> close();
        return $response;
    }
}
