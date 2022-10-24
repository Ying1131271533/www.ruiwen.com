<?php

// Base
namespace app\common\command\chat;

use app\common\logic\lib\Redis;

class BaseChat
{
    protected $redis = null;

    public function __construct()
    {
        $this->redis = new Redis();
    }

    // fd和uid之间的关系的建立
    public function handle($token, $type, $ws, $fd)
    {
        $user = $this->getUser($token);
        dump($user);
        if (empty($user)) {
            $ws->close($fd);
            // 因为swoole是异步执行的，所以想关闭连接并且exit退出程序的话
            // 后面的代码还是会被执行，需要使用swoole提供的exit，这样就不用下面那个else了
            // $this->swooleExit();
        } else {
            // 红叶的折中方法就是用else，他说一般不使用else
            // $result = new \Swoole\Server->bind($fd, $user['id']);
            // fd绑定uid，如果重连的话，那么这里会绑定的不再是fd:1，是swoole重新分配的fd
            $ws->bind($fd, $user['id']);
            if (strpos($type, 'chat_uid_') !== false) {
                $this->setFd($ws, $user['id'], $fd, $type);
            }
            $this->success($ws, $fd, $this->getSocket($user['id']));
        }
    }

    public function setFd($ws, $uid, $fd, $type)
    {
        $data              = $this->getSocket($uid);
        dump($data);
        // type替换新的chat_uid_
        $data['fd'][$type] = $fd;
        // dump($data);
        // 去除无效的fd绑定uid
        foreach ($data['fd'] as $key => $value) {
            // dump($fd);
            // dump($value);
            // 如果重连了，那么这里获取到fd:1的信息就没有绑定uid了，因为swoole重新分配fd了
            // 清除断开连接无效的fd
            $info = $ws->getClientInfo($value);
            // dump($uid);
            dump($info);
            if (empty($info['uid']) || $info['uid'] != $uid) {
                unset($data['fd'][$key]);
            }
            dump('结束');
        }
        // 缓存新的fd到redis
        $this->redis->set(config('redis.socket_pre') . $uid, $data);
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
    public function swooleExit()
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