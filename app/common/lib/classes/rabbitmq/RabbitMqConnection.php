<?php

namespace app\common\lib\classes\rabbitmq;

use PDO;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;


// 参考不良人老师
class RabbitMqConnection
{
    public static function getConnection()
    {
        try {
            return new AMQPStreamConnection(
                config('app.rabbitmq.host'),
                config('app.rabbitmq.port'),
                config('app.rabbitmq.login'),
                config('app.rabbitmq.password'),
                config('app.rabbitmq.vhost')
            );
        } catch (\Throwable $th) {
            throw $th;
        }
        return null;
    }

    public static function closeConnectionAndChannel($connection, $channel)
    {
        try {
            if(!$connection != null) $connection->close();
            if(!$channel != null) $channel->close();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
