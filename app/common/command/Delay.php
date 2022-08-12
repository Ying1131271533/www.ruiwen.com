<?php

// 延迟队列
namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use PhpAmqpLib\Wire\AMQPTable;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class Delay extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('delay');
    }

    protected function execute(Input $input, Output $output)
    {
        // 连接
        $connection = RabbitMqConnection::getConnection();
        // 信息通道
        $channel = $connection->channel();

        // 死信队列名称
        $delay_queue = 'delay_queue';
        // 死信交换机名称
        $delay_exchange = 'delay_exchange';
        // 死信routing_key
        $delay_routing_key = 'delay_routing_key';

        // 声明交换机
        $channel->exchange_declare($delay_exchange, 'direct', false, false, false);
        // 如果要先启动消费者，必须要声明队列
        // $channel->queue_declare($delay_exchange, false, true, false, false);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($delay_queue, $delay_exchange, $delay_routing_key);

        echo "[*] Waiting for logs. To exit press CTRL+C \n";
        // 回调
        $callback = function ($msg) {
            echo " 延迟消费者: $msg->body \n";
            // 确认消息
            $msg->ack();
            // 保存到日志
            Log::info(' 收到延迟队列消息: ' . $msg->body);
        };

        // 设置消费成功后，才能继续下一个消费
        $channel->basic_qos(null, 1, null);

        // 开启消费: no_ack = false，设置为手动应答
        $channel->basic_consume($delay_queue, '', false, false, false, false, $callback);

        // 不断循环消费消息
        while ($channel->is_open()) {
            $channel->wait();
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);
    }

}
