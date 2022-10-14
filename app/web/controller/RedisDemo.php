<?php

namespace app\web\controller;

use app\common\lib\classes\SentinelWork;
use app\common\lib\exception\Fail;
use app\Request;
use app\web\logic\DosecKill;
use think\cache\driver\Redis;

class RedisDemo
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
     * 秒杀
     */
    public function dosecKill(Request $request)
    {
        // 获取参数
        $params     = $request->params;
        $product_id = $params['product_id'];
        $user_id    = $params['user_id'];
        // $user_id = rand(1, 10000000);
        
        // 增加库存
        $stock_key = "sk:" . $product_id . ":qt";
        // $this->redis->set($stock_key, 100);return;
        
        // $this->redis->set('num', 1);
        $user_id = $this->redis->get('num');
        $this->redis->set('num', ++$user_id);
        
        // halt($params);
        // 普通
        // $result = DosecKill::getDosecKill($user_id, $product_id);
        // Lua脚本
        $result = DosecKill::getDosecKillLuaScript($user_id, $product_id);
        if (!$result) {
            throw new Fail('抢购失败');
        }

        return success('抢购成功');
    }

    /**
     * 哨兵
     */
    public function sentinel()
    {
        // 通过SentinelWork处理后返回的master端口和ip，来创建redis对象
        $redis = new SentinelWork();
        // 设置值
        $resutl = $redis->set('num', 1);
        halt($resutl);
    }

    /**
     * 集群 - 暂时不知道怎么连接
     */
    public function cluster()
    {
        // 创建集群对象

    }

}
