<?php

// RabbitMQ 广播 练习
namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class Fanout extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('fanout');
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();

        // 通道绑定交换机
        // $channel->exchange_bind('fanout_exchange', 'fanout_exchange');
        // 弹幕1：消费者的通道是不需要绑定交换机的，这步多余了，可以看官方文档
        // 弹幕2：前面的瞎解释，一个不需要绑定那是mq给你自动查找了，两个交换机时你不绑定试试，炸不死你。。。不懂就跟着学。
        // 弹幕3：发布者将消息发布给交换机，交换机根据路由规则发布到符合队则的queue，但是消费者这里没有和交换机有直接交集，那为什么还要绑定交换机呢？
        // 理解：难怪大佬的代码没有绑定交换机，原来消息已经发到了符合队列规则的queue里面了，那消费者只需要去队列拿就好了

        // 获取临时队列名称
        list($queue_name) = $channel->queue_declare(
            "", //队列名称
            false, //don't check if a queue with the same name exists 是否检测同名队列
            true, //the queue will not survive server restarts 是否开启队列持久化
            true, //the queue might be accessed by other channels 队列是否可以被其他队列访问
            false//the queue will be deleted once the channel is closed. 通道关闭后是否删除队列
        );
        // halt($queue_name);
        // 绑定队列和交换机
        $channel->queue_bind($queue_name, 'logs');
        echo "[*] Waiting for logs. To exit press CTRL+C \n";

        $callback = function ($msg) {
            echo '消费者1: ';
            // echo '消费者2: ';
            echo " $msg->body \n";
            // 保存到日志，这里可以用于日后错误日志的保存
            Log::error("Msg: $msg->body");
        };
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        // 监听通道消息
        while (count($channel->callbacks)) {
            $channel->wait();
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
    }

}
