<?php

namespace app\web\controller;

use lib\ConsistentHash;
use think\cache\driver\Memcache;
use think\facade\Cache;

class MemcacheTest
{
    // TP6框架使用memcache缓存
    public function index()
    {
        // $result = Cache::store('memcache')->set('jinx', '爆爆');
        $result = Cache::store('memcache')->get('jinx');
        return success($result);
    }

    // 设置缓存
    public function set()
    {
        // set()方式是以覆盖的形式赋值
        // add()方式，如果有值了的话，则不能赋值

        $memcahce = new Memcache();
        // 4.设置缓存数据
        // 基本数据类型
        $int     = 123;
        $float   = 12.345;
        $string  = '金克丝';
        $boolean = true;

        // 复合数据类型
        $array = [
            ['id' => 1, 'username' => '金克丝', 'password' => '123456'],
            ['id' => 2, 'username' => '阿卡丽', 'password' => '123456'],
            ['id' => 3, 'username' => '锐雯', 'password' => '123456'],
        ];
        // $array = serialize($array); // memcache底层现实自动序列化

        // 缓存世间
        $expires = 100;
        // 1.缓存的key 2.缓存的value 3.缓存的时间[如果是0表示长期有效，默认30天，]
        // $result   = $memcahce->set('int_set', $int, $expires);
        // $result = $memcahce->get('int_set');

        // $result   = $memcahce->set('float_set', $float, $expires);
        // $result = $memcahce->get('float_set');

        // $result   = $memcahce->set('string_set', $string, $expires);
        // $result = $memcahce->get('string_set');

        // $result   = $memcahce->set('boolean_set', $boolean, $expires);
        // $result = $memcahce->get('boolean_set');

        // 如何设置超过30天的有效期，使用时间戳：time() + 86400 * 31，windows系统不能使用
        // $time = time() + 86400 * 32;
        // $result   = $memcahce->set('array_set', $array, $expires);
        $result = $memcahce->get('array_set');

        return success($result);
    }

    // 分布式的缓存系统 11212 11213 11214 memcache自带的取模
    public function fenbu()
    {
        $memcache = new Memcache();
        // 2. 将多台服务器添加为memcache的分布式系统
        $ip = '127.0.0.1';

        $memcache->addServer($ip, 11212); // 0号memcache服务器
        $memcache->addServer($ip, 11213); // 1号memcache服务器
        $memcache->addServer($ip, 11214); // 2号memcache服务器

        // 3．直接缓存数据【注意︰分布的算法在memcache的内部使用的取模算法,
        // 不需要程序员的参与, memcache内部自动的完成】
        // $reuslt = $memcache->set('username', '金克丝'); // 后面的参数没有给 1.默认不压缩 2.缓存时间默认为0，代表长期有效，默认是30天有效
        $reuslt = $memcache->get('username');

        return success($reuslt);
    }

    // 分布式 - 一致性哈希算法
    public function hash()
    {
        // $key = 'akali';
        $key = 'jinx';
        // 获取能连接的服务器
        $serverArray = get_memcache_server();
        // 获取与key节点匹配的服务器
        $consistentHash = new ConsistentHash();
        $connect = $consistentHash->connect($key, $serverArray);
        // halt($connect);

        $memcache = new Memcache();
        $memcache->connect($connect['host'], $connect['port']);
        // $result = $memcache->set($key, '爆爆');
        $result = $memcache->get($key);
        return success($result);
    }
}
