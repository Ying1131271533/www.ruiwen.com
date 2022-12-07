<?php

namespace app\common\logic\lib;

use think\cache\driver\Memcache as DriverMemcache;

class Memcache
{
    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/04/14 10:45
     *
     * 获取能够连接的memcache服务器
     *
     * @param  array    $serverConfArr  服务器连接配置数组
     * @return array    $serverConfArr  返回连接成功的服务器ip、端口数据
     */
    public function getMemcacheServer($serverConfArr = [])
    {
        $serverConfArr or $serverConfArr = config('app.memcache_server');
        foreach ($serverConfArr as $key => $value) {
            $mem      = explode(':', $value);
            $host     = $mem[0];
            $port     = $mem[1];
            $memcache = new DriverMemcache();
            try {
                $memcache->connect($host, $port);
            } catch (\Exception $e) {
                unset($serverConfArr[$key]);
            }
        }

        if (empty($serverConfArr)) {
            return fail('所有memcache服务器都无法连接');
        }

        return $serverConfArr;
    }
}
