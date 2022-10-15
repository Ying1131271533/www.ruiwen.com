<?php

namespace app\web\controller;

use function \Swoole\Coroutine\run;
use \Swoole\Coroutine\Client;

class SwooleTest
{
    public function index()
    {
        run(function () {
            $client = new Client(SWOOLE_SOCK_TCP);
            if (!$client->connect(config('app.swoole.host'), config('app.swoole.port'), 0.5)) {
                // 连接失败时显示的信息
                echo "connect failed. Error: {$client->errCode}\n";
            }
            // 向服务端发送消息
            $client->send("hello world\n");
            echo $client->recv();
            $client->close();
        });

    }
}
