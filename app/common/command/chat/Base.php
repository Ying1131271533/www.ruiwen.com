<?php

// Base
namespace app\common\command\chat;

use app\common\logic\lib\Redis;
use app\common\model\api\User as UserModel;
use think\console\Command;

class Base extends Command
{
    protected $redis     = null;
    protected $userModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->redis     = new Redis();
        $this->userModel = new UserModel();
    }

    // fd和uid之间的关系的建立
    public function handle($token, $type, $ws, $fd)
    {
        $user = $this->getUser($token);
        // dump($type);
        // dump($user);
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
            // 聊天主面板
            if ($type == 'index') {
                $this->setFd($ws, $user['id'], $fd, $type);
                $this->readDelay($ws, $user['id'], $fd);
            }
        }
    }

    // 读取延时消息，没上线时，别人发的消息
    public function readDelay($ws, $uid, $fd)
    {
        // 获取socket
        $data = $this->getSocket($uid);

        // 好友申请列表的延时数据
        if (!empty($data['apply_list'])) {
            foreach ($data['apply_list'] as $key => $value) {
                $user = $this->userModel->findByIdWithStatus($key);
                // 对方是否被禁用了，禁用就删除申请
                if (empty($user)) {
                    unset($data['apply_list'][$key]);
                    continue;
                }
                $this->success($ws, $fd, [
                    'type'     => 'addFriend',
                    'from'     => $key, // 来自对方的id
                    'username' => $user['username'],
                    'message'  => $value,
                ]);
            }
        }

        // 好友消息的延时数据
        if (!empty($data['delay_list'])) {
            foreach ($data['delay_list'] as $key => $value) {
                $this->success($ws, $fd, [
                    'type'    => 'chat',
                    'fid'     => $key, // 对方的id
                    'count'   => $value['count'],
                    'message' => $value['message'],
                ]);
            }
            // 发送过去后，就删除缓存
            // 不过我觉得应该是对方打开了聊天窗口再删除缓存
            // unset($data['delay_list'][$key]);
        }
        // 缓存socket
        $this->redis->set(config('redis.socket_pre') . $uid, $data);
    }

    // 缓存fd
    public function setFd($ws, $uid, $fd, $type)
    {
        $data = $this->getSocket($uid);
        // dump($data);
        // type替换新的chat_uid_
        $data['fd'][$type] = $fd;
        // dump($data);
        // 去除无效的fd绑定uid
        foreach ($data['fd'] as $key => $value) {
            // dump($fd);
            // dump($value);
            // 如果重连了，那么这里获取到fd:1的信息就没有绑定uid了，因为swoole重新分配fd了
            // 清除断开连接无效的fd
            $bindUid = $this->getBindUid($ws, $value);
            // dump($uid);
            // dump($info);
            if (empty($bindUid) || $bindUid != $uid) {
                unset($data['fd'][$key]);
            }
            // dump('结束');
        }
        // 缓存新的fd到redis
        $this->redis->set(config('redis.socket_pre') . $uid, $data);
    }

    // 获取正在连接的socket绑定的uid
    public function getBindUid($ws, $fd)
    {
        $info = $ws->getClientInfo($fd);
        return empty($info['uid']) ? null : $info['uid'];
    }

    // 获取缓存中的socket数据
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
