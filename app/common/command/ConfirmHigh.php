<?php

// 发布确认 - 高级 消息回退
namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class ConfirmHigh extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('confirm_high');
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取消息通道
        $channel = $connection->channel();

        // 交换姬名称
        $confirm_exchange = 'confirm_exchange';
        // 队列名称
        $confirm_queue = 'confirm_queue';
        // 路由键
        $confirm_routing_key = 'akali';
        // $confirm_routing_key = 'jinx';

        // 声明交换姬
        $channel->exchange_declare($confirm_exchange, 'direct', false, true, false);
        // 声明队列
        // $channel->queue_declare($confirm_queue, false, true, false, false);
        // 将交换姬和队列进行绑定，并且指定routing_key
        $channel->queue_bind($confirm_queue, $confirm_exchange, $confirm_routing_key);

        echo "[*] Waiting for logs. To exit press CTRL+C \n";
        // 回调
        $callback = function($msg){
            echo "发布确认高级队列 - 消费者: $msg->body \n"; 
            $msg->ack();
            Log::info(' 收到发布确认高级队列的消息: ' . $msg->body);
        };
        
        // 消费消息
        $channel->basic_consume($confirm_queue, '', false, false, false, false, $callback);

        // 监听消息
        while($channel->is_open()){
            $channel->wait();
        }
    }

}
