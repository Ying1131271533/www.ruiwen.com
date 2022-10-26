<?php

namespace app\common\logic\lib;

use think\cache\driver\Redis as DriverRedis;
use think\facade\Cache;

class Redis
{
    private $store = null;
    private $redis = null;

    public function __construct($store = 'redis')
    {
        $this->setStore($store);
        // 下面这个不能用setStore()这个方法
        $this->redis = new DriverRedis(config('cache.stores.' . $this->store));
    }

    public function set($key, $value, $ttl = null)
    {
        return Cache::store($this->store)->set($key, $value, $ttl);
        // return $this->redis->set($key, $value, $ttl);
    }

    public function drset($key, $value, $ttl = null)
    {
        return $this->redis->set($key, $value, $ttl);
    }

    public function get($key)
    {
        return Cache::store($this->store)->get($key);
        // return $this->redis->get($key);
    }

    public function delete($key)
    {
        return Cache::store($this->store)->delete($key);
        // return $this->redis->delete($key);
    }

    // 事务
    public function multi()
    {
        return $this->redis->multi();
    }

    // 提交
    public function exec()
    {
        return $this->redis->exec();
    }

    // 回滚
    public function discard()
    {
        return $this->redis->discard();
    }

    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }
}
