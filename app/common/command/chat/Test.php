<?php

// 聊天室
namespace app\common\command\chat;

use app\common\logic\command\Test as TestLogic;
use Swoole\WebSocket\Server;
use think\console\Input;
use think\console\Output;

class Test extends Base
{
    protected $testLogic = null;
    protected $ws        = null;
    public function __construct()
    {
        parent::__construct();
        $this->testLogic = new TestLogic();
        $this->ws        = new Server('0.0.0.0', 9502);
        // $this->ws = new Server('0.0.0.0', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);

        // 用于设置运行时的各项参数
        $this->ws->set([
            // 设置异步任务的工作进程数量
            'task_worker_num' => 4,
            // ssl证书文件的位置
            // 'ssl_cert_file'   => '/etc/nginx/cert/nginx.crt',
            // key的位置
            // 'ssl_key_file'    => '/etc/nginx/cert/nginx.key',
        ]);
    }

    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('test');
    }

    protected function execute(Input $input, Output $output)
    {
        // 监听连接打开事件
        $this->ws->on('Open', [$this, 'onOpen']);

        // 监听消息事件
        $this->ws->on('Message', [$this, 'onMessage']);

        // 处理异步任务(此回调函数在task进程中执行)
        $this->ws->on('Task', [$this, 'onTask']);

        // 处理异步任务的结果(此回调函数在worker进程中执行)
        $this->ws->on('Finish', [$this, 'onFinish']);

        // 监听连接关闭事件
        $this->ws->on('Close', [$this, 'onClose']);

        //启动服务器
        $this->ws->start();
    }

    // 监听WebSocket连接打开事件
    public function onOpen($ws, $request)
    {
        // 这里需要用postman
        // dump($request->get);
        // $this->handle($request->get['token'], 'chat_uid_2', $ws, $request->fd);
        $ws->push($request->fd, "客户端：{$request->fd}，打开连接，进入聊天室\n");
    }

    // 监听WebSocket发送信息
    // 参数1：所有连接数据
    // 参数2：正在发送消息的用户的连接数据
    // ！！！我在这里测试的时候出现了一个问题，连接fd有1、2、4
    // 一共产生了fd为1、2、3、4、5的连接，观察命令行发现3和5关闭了连接，但是2还保持着连接
    // 然后$ws里面的连接fd没有2，导致$ws-push发送不了
    // 解决方法：使用$ws->isEstablished($fd)判断是否为有效连接，或者使用try
    public function onMessage($ws, $frame)
    {
        $this->testLogic->handle($ws, $frame);
    }

    // 处理异步任务(此回调函数在task进程中执行)
    // $task_id 有可能会出现相同
    // 【$task_id 和 $src_worker_id 组合起来才是全局唯一的，不同的 worker 进程投递的任务 ID 可能会有相同】
    public function onTask($ws, $task_id, $reactor_id, $data)
    {
        return $data;
        // echo "New AsyncTask[id={$task_id}]" . PHP_EOL;
        // 返回任务执行的结果
        // $ws->finish("{$data} -> OK");
    }

    // 处理异步任务的结果(此回调函数在worker进程中执行)
    public function onFinish($ws, $task_id, $data)
    {
    }

    // 监听WebSocket连接关闭事件
    // 只要关闭了连接，就一定会触发这里的onClose()
    public function onClose($ws, $fd)
    {
        echo "客户端-{$fd}：关闭连接\n";
    }
}