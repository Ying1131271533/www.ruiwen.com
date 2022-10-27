<?php

namespace app\common\model\api;

use think\Model;

class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'api_user';
    // 设置当前模型的数据库连接
    protected $connection = 'mysql';
    
    // 好友列表
    public function friends()
    {
        return $this->hasMany(Friend::class, 'uid');
    }

    // 查询用户，使用名称
    public function findByUserName($username)
    {
        return $this->where('username', $username)->find();
    }

    // 查询用户，使用id，根据状态1
    public function findByIdWithStatus($id)
    {
        return $this->where('id', $id)->where('status', 1)->find();
    }

    // 查询用户名，根据状态1
    public function findByUserNameWithStatus($username)
    {
        return $this->where('username', $username)->where('status', 1)->find();
    }

    // 更新登录信息
    public function updateLoginInfo($data)
    {
        // 找到用户
        $user = $this->findByUserNameWithStatus($data['username']);
        // 只允许部分字段更新
        return $user->allowField(['last_login_token'])->save($data);
    }
}
