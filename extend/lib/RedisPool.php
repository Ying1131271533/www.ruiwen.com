<?php
namespace lib;

use think\cache\driver\Redis;

class RedisPool
{
    private static $connections = array(); //定义一个对象池
    private static $servers     = array(); //定义redis配置文件

    // 定义添加redis配置方法
    public static function addServer($conf)
    {
        foreach ($conf as $alias => $data) {
            self::$servers[$alias] = $data;
        }
    }

    // 两个参数，要连接的服务器KEY, 要选择的库
    public static function getRedis($alias = "RA", $select = 0)
    {
        // 判断连接池中是否存在
        if (!array_key_exists($alias, self::$connections)) {
            $redis = new Redis();
            $redis->connect(self::$servers[$alias][0], self::$servers[$alias][1]);
            self::$connections[$alias] = $redis;
            if (isset(self::$servers[$alias][2]) && self::$servers[$alias][2] != "") {
                self::$connections[$alias]->auth(self::$servers[$alias][2]);
            }
        }
        self::$connections[$alias]->select($select);
        return self::$connections[$alias];
    }
}

/* require 'RedisPool.php';
$conf = array( 
    'RA' => array('127.0.0.1', 6379)   //定义Redis配置
);
RedisPool::addServer($conf); //添加Redis配置
$redis = RedisPool::getRedis('RA'); //连接RA，使用默认0库
$redis->set('user','private');
echo $redis ->get('user'); */