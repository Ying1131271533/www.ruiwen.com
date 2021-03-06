<?php

// RabbitMQ 消费者 练习

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

// 创建连接对象
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'akali', '123456', '/akali');
// $connection = new AMQPStreamConnection('192.168.0.184', 5672, 'akali', '123456', '/akali');
// $connection = new AMQPStreamConnection('rabbitmq', 5672, 'akali', '123456', '/akali');

// 创建通道
$channel = $connection->channel();

// 通道绑定对象
// 这里要注意的是，生产者和消费者的队列参数必需一致
// 参数1：队列名称
$channel->queue_declare('akali_queue', false, true, false, true);
// $channel->queue_declare('hello', false, false, false, true);

echo ' [*] Waiting for messages. To exit press Ctrl+C', "\n";

// 回调函数
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";    

    // 判断获取到quit后退出
    if (trim($msg->body) == 'quit') {
        $msg->getChannel()->basic_cancel($msg->getConsumerTag());
    }
};

// 消费信息
// 参数1:消费那个队列的消息队列名称
// 参数2:虚拟主机
// 参数4:开始消息的自动确认机制
// 参数7:消费时的回调接口
$channel->basic_consume('akali_queue', '', false, true, false, false, $callback);
// $channel->basic_consume('hello', '', false, true, false, false, $callback);

// 监听通道消息
while (count($channel->callbacks)) {
    $channel->wait();
}

// 如果只希望消息一次就关闭连接，一般不关闭
$channel->close();
$connection->close();
