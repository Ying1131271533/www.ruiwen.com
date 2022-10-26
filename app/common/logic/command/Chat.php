<?php

namespace app\common\logic\command;

use app\common\command\chat\Base;
use app\common\logic\lib\Redis;

class Chat extends Base
{
    // 因为没有parent::__construct();
    // 所以这里需要重新实例化redis
    protected $redis = null;

    public function __construct()
    {
        $this->redis = new Redis();
    }

    public function switchboard($ws, $frame)
    {
        $data = json_decode($frame->data, true);
        // 聊天类型
        switch ($data['type']) {
            // 加好友
            case 'addFriend':
                $this->addFriend($ws, $frame->fd, $data);
                break;

            default:
                # code...
                break;
        }
    }

    private function addFriend($ws, $fd, $data)
    {
        // 获取对方的id
        $socket = $this->getSocket($data['target']);
        $socket['apply_list'][$data['uid']] = $data['message'];
        // 对方是否在线，主面板fd
        if (!empty($socket['fd']['index'])) {
            // 如果在线，则直接推送消息
            $this->success($ws, $socket['fd']['index'], [
                'type'     => 'addFriend',
                'from'     => $data['uid'],
                'username' => $data['username'],
                'message'  => $data['message'],
            ]);
        }
        $this->redis->set(config('redis.socket_pre') . $data['target'], $socket);
        $this->success($ws, $fd, null);
    }
}
