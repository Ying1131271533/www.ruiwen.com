<?php

namespace app\common\logic\command;

use app\common\command\chat\Base;
use app\common\logic\lib\Redis;
use app\common\model\api\Chat as ChatModel;

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
            // 聊天消息
            case 'chat':
                $this->chat($ws, $frame->fd, $data);
                break;
            default:
                # code...
                break;
        }
    }

    // 聊天 我改的
    private function chat($ws, $fd, $data)
    {
        // 获取bind的uid
        $uid = $this->getBindUid($ws, $fd);
        ChatModel::create([
            'uid'     => $uid,
            'fid'     => $data['fid'],
            'message' => $data['message'],
            // 'create_time' => time()
        ]);

        // 获取对方的socket
        $socket = $this->getSocket($data['fid']);
        // 对方打开了聊天窗口
        if (isset($socket['fd']['chat_uid_' . $uid])) {
            $this->success($ws, $socket['fd']['chat_uid_' . $uid], [
                'message' => $data['message'],
            ]);
        } else {

            // 对方打开了主面板或者是离线状态

            // 组装数据
            if (isset($socket['delay_list'][$uid]['count'])) {
                $socket['delay_list'][$uid]['count'] += 1;
                $socket['delay_list'][$uid]['message'] = $data['message'];
            } else {
                $socket['delay_list'][$uid] = [
                    'count'   => 1,
                    'message' => $data['message']
                ];
            }

            // 如果对方有打开主面板，则发送消息
            if (isset($socket['fd']['index'])) {
                $this->success($ws, $socket['fd']['index'], [
                    'type'    => 'chat',
                    'fid'     => $uid, // 自己的uid则是对方的好友id
                    'count'   => $socket['delay_list'][$uid]['count'],
                    'message' => $data['message']
                ]);
            }
            
            // 保存
            $this->redis->set(config('redis.socket_pre') . $data['fid'], $socket);
        }
    }

    // 红叶原代码
    private function shane_chat($ws, $fd, $data)
    {
        // 获取bind的uid
        $uid = $this->getBindUid($ws, $fd);
        ChatModel::create([
            'uid'     => $uid,
            'fid'     => $data['fid'],
            'message' => $data['message'],
            // 'create_time' => time()
        ]);

        // 获取对方的socket
        $socket = $this->getSocket($data['fid']);
        if (isset($socket['fd']['chat_uid_' . $uid])) {
            // 对方打开窗口
            $this->success($ws, $socket['fd']['chat_uid_' . $uid], [
                'message' => $data['message'],
            ]);
        } else if (isset($socket['fd']['index'])) {
            // 对方打开主面板
            $this->success($ws, $socket['fd']['index'], [
                'type'    => 'chat',
                'uid'     => $uid,
                'count'   => 1,
                'message' => $data['message']
            ]);
            
        } else {
            // 离线状态
            if (isset($socket['delay_list'][$uid]['count'])) {
                $socket['delay_list'][$uid]['count'] += 1;
                $socket['delay_list'][$uid]['message'] = $data['message'];
            } else {
                $socket['delay_list'][$uid] = [
                    'count'   => 1,
                    'message' => $data['message']
                ];
            }
            // 保存
            $this->redis->set(config('redis.socket_pre') . $data['fid'], $socket);
        }
    }

    // 添加好友
    private function addFriend($ws, $fd, $data)
    {
        // 获取对方的id
        $socket                             = $this->getSocket($data['target']);
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
