<?php

namespace app\common\lib\classes\rabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;


// 参考不良人老师
class RabbitMqConnection
{
    public static function getConnection(array $config = [])
    {
        try {
            $config = array_replace(config('app.rabbitmq'), $config);
            return new AMQPStreamConnection(
                $config['host'],
                $config['port'],
                $config['login'],
                $config['password'],
                $config['vhost']
            );
        } catch (\Throwable $th) {
            throw $th;
        }
        return null;
    }

    public static function closeConnectionAndChannel($channel, $connection)
    {
        try {
            if(!$connection != null) $connection->close();
            if(!$channel != null) $channel->close();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
