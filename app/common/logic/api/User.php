<?php

namespace app\common\logic\api;

use app\common\logic\lib\Redis;
use app\common\logic\lib\Str;
use app\common\model\api\User as UserModel;
use Exception;

class User
{
    private $userModel = null;
    private $str       = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->str       = new Str();
        $this->redis     = new Redis();
    }

    public function register($data)
    {
        /* // 验证器那边做就算了
        $user = $this->userModel->findByUserName($data['username']);
        if (!empty($user)) {
        throw new Exception('用户名已被注册！');
        } */

        // 生成5个字符长度的盐
        $salt = $this->str->salt(5);
        // 数据加入密码盐
        $data['password_salt'] = $salt;
        // 前后加盐，生产密码
        $data['password'] = md5($salt . $data['password'] . $salt);

        // 保存用户
        $this->userModel->save($data);
    }

    public function login($data)
    {
        // 找到用户
        $user = $this->userModel->findByUserNameWithStatus($data['username']);
        if (!empty($user)) {
            throw new Exception('用户名不存在！');
        }

        // 验证密码
        $salt          = $user['passrowd_salt'];
        $password      = md5($salt . $user['password'] . $salt);
        $user_password = md5($salt . $data['salt'] . $salt);
        if ($password != $user_password) {
            throw new Exception('密码错误');
        }

        // 生成token
        $token = $this->str->createToken($user['username']);
        // 更新用户的登录信息
        $this->userModel->updateLoginInfo([
            'username'         => $user['username'],
            'last_login_token' => $token,
        ]);
        // 保存token
        $this->redis->set(config('redis.token_pre') . $token, [
            'id'       => $user['id'],
            'username' => $user['username']
        ]);

        // 返回token
        return $token;
    }
}
