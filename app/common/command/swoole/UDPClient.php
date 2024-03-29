<?php

// TCP客户端
namespace app\common\command\swoole;

use function \Swoole\Coroutine\run;
use \Swoole\Coroutine\Client;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class UDPClient extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('udp_client');
    }

    protected function execute(Input $input, Output $output)
    {
        run(function () {

            // 客户端连接
            // 参数1：类型为UDP
            $client = new Client(SWOOLE_SOCK_UDP);
            if (!$client->connect(config('app.swoole.host'), config('app.swoole.port_udp'), 0.5)) {
                // 连接失败时显示的信息
                echo "connect failed. Error: {$client->errCode}\n";
            }

            // 命令行输入信息
            fwrite(STDOUT, '请输入消息：');
            // 接收信息
            $msg = fgets(STDIN);

            // 向服务端发送消息
            $client->send("$msg\n");
            // 显示服务端返回的消息
            echo $client->recv();

            // 关闭连接
            $client->close();
        });
    }

}
