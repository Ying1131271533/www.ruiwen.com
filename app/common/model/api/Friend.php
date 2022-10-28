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

    // 用户表
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 找到用户的好友，然后在通过fid找到好友的id，获取用户表信息
    // 使用bind()指定只要好友的用户名字段，这样的话
    // 用户的数据就会合并到好友数据里面，而不是变成子级数据了
    public function username()
    {
        return $this->belongsTo(User::class, 'fid')->bind(['username']);
    }

    // 是否为好友
    public function isFriend($uid, $fid)
    {
        $user   = $this->where('uid', $uid)->where('fid', $fid)->where('status', 1)->find();
        $friend = $this->where('uid', $fid)->where('fid', $uid)->where('status', 1)->find();
        // 如果有一方为空，则不是好友
        return !empty($user) && !empty($friend);
    }
    // 是否为黑名单
    public function isBlack($uid, $fid)
    {
        $user = $this->where('uid', $uid)->where('fid', $fid)->where('status', 0)->find();
        if (!empty($user)) {
            throw new Exception('对方在您的黑名单中');
        }

        $friend = $this->where('uid', $fid)->where('fid', $uid)->where('status', 0)->find();
        if (!empty($friend)) {
            throw new Exception('您在对方的黑名单中');
        }
    }

    // 好友列表
    public function friendList($uid)
    {
        return self::with('username')   
            ->where('uid', $uid)
            ->where('status', 1)
            ->field(['fid'])
            ->select();
            // 红叶说这里应该做成分页，但是要传分页参数过来
            // ->paginate($size, false, ['page' => $page]);
    }
}
