<?php

// WebSocket服务端
namespace app\common\command\swoole;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class WebSocket extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('web_socket');
    }

    protected function execute(Input $input, Output $output)
    {
        // 创建WebSocket Server对象，监听0.0.0.0:9502端口
        // 参数3：多进程模式
        // 参数4：WebSocket是基于tcp做成的协议，打开ssl
        $ws = new \Swoole\WebSocket\Server('0.0.0.0', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
        // 不使用ssl
        // $ws = new \Swoole\WebSocket\Server('0.0.0.0', 9502);

        // 用于设置运行时的各项参数
        // 需要先开启这里的程序，再去浏览器打开static里的web_socket.html
        $ws->set([
            // 静态文件
            'enable_static_handler'  => true,
            // 根目录，放置html等其它的静态文件，直接访问
            'document_root' => '/var/www/www.ruiwen.com/app/common/command/swoole/static',
            // ssl证书文件的位置
            'ssl_cert_file' => '/etc/nginx/ssl/nginx.crt',
            // key的位置
            'ssl_key_file'  => '/etc/nginx/ssl/nginx.key',
        ]);

        // 监听WebSocket连接打开事件
        $ws->on('Open', [$this, 'onOpen']);

        // 监听WebSocket消息事件
        $ws->on('Message', [$this, 'onMessage']);

        // 监听WebSocket连接关闭事件
        $ws->on('Close', [$this, 'onClose']);

        //启动服务器
        $ws->start();
    }

    // 监听WebSocket连接打开事件
    public function onOpen($ws, $request)
    {
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "客户端：{$request->fd}，打开连接，进入聊天室\n");
    }

    // 监听WebSocket连接打开事件
    // 参数1：所有连接数据
    // 参数2：正在发送消息的用户的连接数据
    public function onMessage($ws, $frame)
    {
        dump($frame->fd);
        echo "消息: {$frame->data}\n";
        // WebSocket会存储所有用户连接进来的fd
        foreach ($ws->connections as $fd) {
            // 场景设定为1:1聊天
            // 和正在连接的fd进行对比，例如我fd-01和对方fd-02正在聊天
            // 判断是我，还是对方发送的消息
            if ($fd == $frame->fd) {
                // 服务端用fd来向客户端用户：我，发送消息，返回消息是告知是我发送的消息
                $ws->push($fd, "我发送的消息: {$frame->data}");
            } else {
                // 服务端用fd来向客户端用户：对方，发送消息，返回消息是告知是对方发送的消息
                $ws->push($fd, "对方发送的消息: {$frame->data}");
            }
        }
    }

    // 监听WebSocket连接关闭事件
    public function onClose($ws, $fd)
    {
        echo "客户端-{$fd}：关闭连接\n";
    }

}
