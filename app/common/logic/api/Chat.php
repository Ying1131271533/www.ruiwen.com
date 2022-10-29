<?php

namespace app\common\logic\api;

use app\common\logic\lib\Redis;
use app\common\logic\lib\Str;
use app\common\model\api\Chat as ChatModel;
use Exception;
use WebSocket\Client;

class Chat
{
    private $chatModel = null;
    private $redis     = null;

    public function __construct()
    {
        $this->chatModel = new ChatModel();
        $this->redis     = new Redis();
    }

    // 获取聊天记录
    public function record($data)
    {
        // 打开了聊天窗口就清除延时聊天记录的缓存
        // 获取对方的socket
        $socket = $this->redis->get(config('redis.socket_pre') . $data['uid']);
        if (isset($socket['delay_list'][$data['fid']])) {
            unset($socket['delay_list'][$data['fid']]);
            $this->redis->set(config('redis.socket_pre') . $data['uid'], $socket);
        }

        // 找到双方的聊天记录，这里的数据是分两次获取的，所以需要合并数组，重新排序
        $myRecord     = $this->chatModel->getRecord($data['uid'], $data['fid'])->toArray();
        $friendRecord = $this->chatModel->getRecord($data['fid'], $data['uid'])->toArray();
        $allRecord    = array_merge($myRecord, $friendRecord);
        $key          = array_column($allRecord, 'create_time');
        array_multisort($key, SORT_ASC, $allRecord);
        return $allRecord;
    }
}
