<?php

namespace app\common\model\api;

use Exception;
use think\Model;

class Chat extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'api_chat';
    // 设置当前模型的数据库连接
    protected $connection = 'mysql';

    // 获取双方的聊天记录
    public function getRecord($uid, $fid)
    {
        return $this->field(['uid', 'message', 'create_time'])
        ->where('uid', $uid)
        ->where('fid', $fid)
        ->order('create_time')
        ->select();
        // 红叶说这里应该做成分页，但是要传分页参数过来...
        // ->paginate($size, false, ['page' => $page]);
    }
}
