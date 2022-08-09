<?php
namespace app\web\logic;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use lib\RedisLock;
use lib\RedisPool;

class DosecKill
{
    // 普通秒杀代码
    public static function getDosecKill(int $user_id, int $product_id): bool
    {
        // $redis = new Redis(['host' => '124.71.218.160', 'password' => 'Ak12al&iYi4%3*@n.!g']);

        RedisPool::addServer(config('app.redis_pool')); // 添加Redis配置
        $redis = RedisPool::getRedis('RA'); // 连接RA，使用默认0库

        // 库存key
        $stockKey = "sk:" . $product_id . ":qt";
        // 秒杀成功用户key
        $userIdKey = "sk:" . $product_id . ":user";

        // 监视库存
        $redis->watch($stockKey);

        // 获取库存，如果库存null，秒杀还没有开始
        $stock = $redis->get($stockKey);
        if ($stock == null) {
            throw new Miss('秒杀活动还没有开始，请稍等');
        }

        // 用户是否重复秒杀
        if ($redis->sismember($userIdKey, $user_id)) {
            throw new Fail('秒杀活动不能重复参加！');
        }

        // 判断如果商品数量，库存数量小于1，秒杀结束
        if ($stock < 1) {
            throw new Fail('秒杀活动已经结束');
        }

        // 秒杀过程
        // 开启事务
        $multi = $redis->multi();

        // 命令组队操作
        $multi->decr($stockKey);
        $multi->sadd($userIdKey, $user_id);

        // 执行
        $resultList = $multi->exec();
        if ($resultList == null || sizeof($resultList) == 0) {
            throw new Fail('秒杀失败');
        }

        return true;
    }

    /**
     * 秒杀活动Lua脚本执行
     *
     * @param  int      $user_id        用户id
     * @param  int      $product_id     商品id
     * @return bool                     返回结果
     */
    public static function getDosecKillLuaScript(int $user_id, int $product_id)
    {
        // $redis = new Redis(['host' => '124.71.218.160', 'password' => 'Ak12al&iYi4%3*@n.!g']);
        RedisPool::addServer(config('app.redis_pool')); // 添加Redis配置
        $redis = RedisPool::getRedis('RA'); // 连接RA，使用默认0库

        // 库存key
        $stockKey = "stock_key:" . $product_id . ":qt";
        // 秒杀成功用户key
        $userIdKey = "stock_key:" . $product_id . ":user";
        
        // 实例化redisLock
        $redisLock = new RedisLock();
        $key       = 'lock_akali';
        if ($redisLock->lockByLua($key)) {
            // to do...业务代码

            // 获取库存，如果库存null，秒杀还没有开始
            $stock = $redis->get($stockKey);
            if ($stock == null) {
                throw new Miss('秒杀活动还没有开始，请稍等');
            }

            // 用户是否重复秒杀
            if ($redis->sismember($userIdKey, $user_id)) {
                throw new Fail('秒杀活动不能重复参加！');
            }

            // 判断如果商品数量，库存数量小于1，秒杀结束
            if ($stock < 1) {
                throw new Fail('秒杀活动已经结束');
            }

            $redis->decr($stockKey);
            $redis->sadd($userIdKey, $user_id);

            // 下面是保存到RabbitMQ里面的业务代码

            // 发送给生成订单数据的RabbitMQ
            // 获取连接
            // $rabbitmqConnection = RabbitMqConnection::getConnection(['vhost' => 'order']);
            // 获取通道
            // $channel = $rabbitmqConnection->channel();
            // $channel->queue_declare('order_mysql', false, true, false, false);
            // $msg = serialize(['user_id' => $user_id, 'goods_id' => $product_id]);
            // $amqpMsg = new AMQPMessage($msg);

            // 发送给需要半小时支付的RabbitMQ
            // 获取连接
            // $rabbitmqConnection = RabbitMqConnection::getConnection(['vhost' => 'order']);
            // 获取通道
            // $channel = $rabbitmqConnection->channel();
            // $channel->queue_declare('order_pay', false, true, false, false);
            // $msg = serialize(['user_id' => $user_id, 'goods_id' => $product_id]);
            // $amqpMsg = new AMQPMessage($msg);

            $result = $redisLock->unlock($key);
            if (!$result) {
                throw new Fail('抢购失败');
            }
            return true;
        } else {
            throw new Fail('抢购失败');
        }
    }
}
