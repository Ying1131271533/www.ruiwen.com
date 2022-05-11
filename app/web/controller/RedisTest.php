<?php

namespace app\web\controller;

use app\lib\exception\Fail;
use app\lib\SentinelWork;
use app\Request;
use app\web\logic\DosecKill;
use think\cache\driver\Redis;

class RedisTest
{
    protected $redis;
    public function __construct()
    {
        // 创建Redis对象
        $this->redis = new Redis(['host' => '127.0.0.1', 'password' => 'Ak-12]al^iY?i4/3@n.!g']);
        // $del = $this->redis->flushdb();exit;
        // $redis_keys = $this->redis->keys('*');
    }

    /**
     * 操作string - 字符串
     */
    public function string()
    {
        $redis = $this->redis;

        // 字符串
        $redis->set('name', 'Akali');
        $akali = $redis->get('name');

        // 设置多个字符串
        $redis->mset(['k1' => 'v1', 'k2' => 'v2']);
        $mset = $redis->mget(['k1', 'k2']);
        return success($mset);
    }

    /**
     * 列表(List)
     */
    function list() {
        $redis = $this->redis;
        $redis->lpush('user', 'Akali', 'Jinx', 'Kaxiu');
        $list = $redis->lrange('user', 0, -1);
        return success($list);
    }

    /**
     * 列表(List) 队列发布数据
     */
    public function listLpush()
    {
        $redis = new Redis(['host' => '127.0.0.1', 'password' => 'Ak-12]al^iY?i4/3@n.!g']);
        $redis-> lpush('lol', 'jinx');
    }

    /**
     * 列表(List) 队列消费数据
     */
    public function listBrpop()
    {
        $redis = new Redis(['host' => '127.0.0.1', 'password' => 'Ak-12]al^iY?i4/3@n.!g']);
        // 没消息阻塞等待，0表示不设置超时时间
        $result = $redis -> brpop('lol', 0);
        return success($result);
    }


    /**
     * 集合(Set)
     */
    public function set()
    {
        $redis = $this->redis;
        $redis->sadd('name', 'Akali', 'Jinx', 'Kaxiu');
        $set = $redis->smembers('name');
        return success($set);
    }

    /**
     * 有序集合(sorted set)
     */
    public function zset()
    {
        $redis = $this->redis;
        $redis->zadd('china', '100d', 'shanghai');
        $zset = $redis->zrange('china', 0, -1);
        return success($zset);
    }

    /**
     * 哈希(Hash)
     */
    public function hash()
    {
        $redis = $this->redis;
        $redis->hset('user-001', 'age', '17');
        $hash = $redis->hget('user-001', 'age');
        return success($hash);
    }

    /**
     * 哈希槽分区 应该要做哨兵
     * 
     * redis集群（不管多少个redis服务器）的哈希槽一共有16384个
     */
    public function hashSlot()
    {
        $redis = $redis = new Redis(['host' => '127.0.0.1', 'port' => '6381']);
        // halt($redis);
        // $redis = $redis = new SentinelWork();
        // $redis->set('jinx', '金克丝');
        $result = $redis->get('jinx');
        // 显示槽点10个数据
        // $slot = $redis->setkeysinslot(5474, 10);
        // 拿到槽点位置
        // $slot = $redis->keyslot('jinx');

        return success($result);
    }

}
