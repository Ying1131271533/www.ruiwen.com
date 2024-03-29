<?php

// 死信队列练习 - 普通消费者
namespace app\common\command\rabbtimq;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use PhpAmqpLib\Wire\AMQPTable;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class Normal extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('normal');
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection(['vhost' => 'order']);
        // 获取通道
        $channel = $connection->channel();

        // 普通交换机名称
        $normal_exchange = 'normal_exchange';
        // 普通队列名称
        $normal_queue = 'normal_queue';
        // 普通routing_key
        $normal_routing_key = 'normal_routing_key';

        // 声明交换机
        $channel->exchange_declare($normal_exchange, 'direct', false, false, false);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($normal_queue, $normal_exchange, $normal_routing_key);

        echo "[*] Waiting for logs. To exit press CTRL+C \n";

        $callback = function ($msg) {
            echo "普通消费者: $msg->body \n";
            // 确认消息已被消费，从生产队列中移除
            // 第三种死信情况 消息被拒
            // 这里如果不应答，那么消息就会跑到死信队列中(需要设置不放回原本的队列)，状态是未确认
            // if ($msg->equals(5)) {
            if ($msg->body == '6') {
                // 参数1：tag
                // 参数2：是否放回原本的队列，因为设置了死信队列，所以会跑到死信队列那边去
                // $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false);
                // 普通拒答
                // 参数1：是否放回原本的队列，默认false，放回队列竟然是放回原本的顺序
                // 如果是true放回队列，那么就会进入死循环
                $msg->nack(false);
                // 参数1：tag
                // 参数2：是否批量不应答
                // $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, false);
                echo "第" . $msg->body . "条消息被拒答\n";
            } else {
                $msg->ack();
                // 参数2：是否批量应答
                // $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag'], false);
            }
        };

        // 设置消费成功后才能继续进行下一个消费
        $channel->basic_qos(null, 1, null);

        // 开启消费no_ack=false,设置为手动应答
        $channel->basic_consume($normal_queue, '', false, false, false, false, $callback);

        // 不断循环消费
        while ($channel->is_open()) {
            $channel->wait();
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);
    }

}
