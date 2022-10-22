<?php

//
namespace app\common\command\chat;

use app\common\logic\lib\Redis;
use Swoole\WebSocket\Server;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Base extends Command
{
    protected $redis = null;

    public function __construct()
    {
        parent::__construct();
        $this->redis = new Redis();
    }

    // fd和uid之间的关系的建立
    public function handle($token, $type, $ws, $fd)
    {
        $user = $this->getUser($token);
        if (empty($user)) {
            $ws->close($fd);
            // 因为swoole是异步执行的，所以想关闭连接并且exit退出程序的话
            // 后面的代码还是会被执行，需要使用swoole提供的exit，这样就不用下面那个else了
            // $this->swooleExit();
        } else {
            // 红叶的折中方法就是用else，他说一般不使用else
            // $result = new \Swoole\Server->bind($fd, $user['id']);
            $ws->bind($fd, $user['id']);
            if (strpos($type, 'chat_uid_') !== false) {
                $this->setFd($ws, $user['id'], $fd, $type);
            }
        }
    }

    public function setFd($ws, $uid, $fd, $type)
    {
        $data = $this->getSocket($uid);
        $data['fd'][$type] = $fd;
        
    }

    public function getSocket($uid)
    {
        return $this->redis->get(config('redis.socket_pre') . $uid);
    }

    public function getUser($token)
    {
        return $this->redis->get(config('redis.token_pre') . $token);
    }

    public function success($ws, $fd, $data)
    {
        $this->show($ws, $fd, config('status.success'), config('message.success'), $data);
    }

    public function fail($ws, $fd, $data)
    {
        $this->show($ws, $fd, config('status.failed'), $data, null);
    }

    public function show($ws, $fd, $status, $message, $result)
    {
        $data = [
            'status'  => $status,
            'message' => $message,
            'result'  => $result,
        ];
        $ws->push($fd, json_encode($data));
    }

    // 因为swoole是异步执行的，所以exit了，后面的代码还是会被执行，需要使用swoole提供的exit
    function swooleExit()
    {
        \Swoole\Coroutine\run(function () {
            try
            {
                exit(0);
            } catch (\Swoole\ExitException $e) {
                echo $e->getMessage() . "\n";
            }
        });

    }
}
