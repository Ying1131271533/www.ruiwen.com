<?php

namespace app\common\lib\classes\rabbitmq;

use PDO;
use PhpAmqpLib\Connection\AMQPStreamConnection;

// 参考不良人老师
class RabbitMqConnection
{
    public static function getConnection()
    {
        try {
            return new AMQPStreamConnection('127.0.0.1', 5672, 'akali', '123456', '/akali');
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
