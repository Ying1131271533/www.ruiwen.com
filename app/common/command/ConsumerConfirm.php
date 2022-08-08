<?php

// RabbitMQ 广播 练习
namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class ConsumerConfirm extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('consumerConfirm');
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();
        
        // 声明队列
        $channel->queue_declare('hello', false, true, false, false);
        // 这里只是看看耗时，不持久化
        // $channel->queue_declare('hello', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press Ctrl+C', "\n";
        
        // 回调函数
        $callback = function ($msg) {
            echo "[x] 消费者-1：", $msg->body, "\n";
            // echo "[x] 消费者-2：", $msg->body, "\n";
            // sleep(1);
            // sleep(5);
            $isAck = true;
            // $isAck = false;
            if ($isAck) {
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
            // 判断获取到quit后退出
            if (trim($msg->body) == 'quit') {
                $msg->getChannel()->basic_cancel($msg->getConsumerTag());
            }
        };

        // 开启消息确认模式
        $channel->basic_consume('hello', '', false, false, false, false, $callback);
        // $channel->basic_consume('hello', '', false, true, false, false, $callback);

        // 监听通道消息，这里没有的话，消费完消息就会自动退出
        while (count($channel->callbacks)) {
            $channel->wait();
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
    }

}
