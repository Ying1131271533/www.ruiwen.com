<?php

namespace app\common\logic\api;

use app\common\logic\lib\Redis;
use app\common\logic\lib\Str;
use app\common\model\api\Friend as FriendModel;
use app\common\model\api\User as UserModel;
use app\common\model\api\Chat as ChatModel;
use Exception;
use WebSocket\Client;

class Chat
{
    private $chatModel = null;

    public function __construct()
    {
        $this->chatModel = new ChatModel();
    }

    // 获取聊天记录
    public function record($data)
    {
        // 找到双方的聊天记录，这里的数据是分两次获取的，所以需要合并数组，重新排序
        $myRecord = $this->chatModel->getRecord($data['uid'], $data['fid'])->toArray();
        $friendRecord = $this->chatModel->getRecord($data['fid'], $data['uid'])->toArray();
        $allRecord = array_merge($myRecord, $friendRecord);
        $key = array_column($allRecord, 'create_time');
        array_multisort($key, SORT_ASC, $allRecord);
        return $allRecord;
    }
}
