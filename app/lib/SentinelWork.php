<?php
/**
 * @description:  オラ!オラ!オラ!オラ!
 * @author: Shane
 * @time: 2020/4/19 19:04
 *
 */
namespace app\lib;

use think\cache\driver\Redis;
use think\facade\Cache;

class SentinelWork
{
    private $master = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => 'Ak-12]al^iY?i4/3@n.!g',
        'select'     => 0,
        'timeout'    => 0
    ];

    public function __construct()
    {
        // 通过sentinel获取的master端口和ip
        $sentinelInfo = Cache::store('sentinel') -> rawCommand('SENTINEL', 'masters');
        $this -> master['host'] = $sentinelInfo[0][3];
        $this -> master['port'] = $sentinelInfo[0][5];
        $redis = new Redis($this -> master);
        return $redis;
    }

    // 自定义set方法
    public function set($key, $value){
        $redis = new Redis($this -> master);
        return $redis -> set($key,$value);
    }

}
