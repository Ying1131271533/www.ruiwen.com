<?php

namespace app\common\model\api;

use Exception;
use think\Model;

class Friend extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'api_friend';
    // 设置当前模型的数据库连接
    protected $connection = 'mysql';

    // 是否为好友
    public function isFriend($uid, $fid)
    {
        $user = $this->where('uid', $uid)->where('fid', $fid)->where('status', 1)->find();
        $friend = $this->where('uid', $fid)->where('fid', $uid)->where('status', 1)->find();
        // 如果有一方为空，则不是好友
        return !empty($user) && !empty($friend);
    }
    // 是否为黑名单
    public function isBlack($uid, $fid)
    {
        $user = $this->where('uid', $uid)->where('fid', $fid)->where('status', 0)->find();
        $friend = $this->where('uid', $fid)->where('fid', $uid)->where('status', 0)->find();
        
        if(!empty($user)) {
            throw new Exception('对方在您的黑名单中');
        }
        
        if(!empty($friend)) {
            throw new Exception('您在对方的黑名单中');
        }
    }
}
