<?php

// RabbitMQ 消费者

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

// 创建连接对象
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'akali', '123456', '/akali');
// $connection = new AMQPStreamConnection('192.168.0.184', 5672, 'akali', '123456', '/akali');
// $connection = new AMQPStreamConnection('rabbitmq', 5672, 'akali', '123456', '/akali');

// 创建通道
$channel = $connection->channel();
// 通道绑定对象
$channel->queue_declare('akali_queue', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// 回调函数
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

// 消费信息
// 参数1:消费那个队列的消息队列名称
// 参数2:开始消息的自动确认机制
// 参数7:消费时的回调接口
$channel->basic_consume('akali_queue', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

// 如果只希望消息一次就关闭连接，一般不关闭
$channel->close();
$connection->close();
