<?php

namespace app\common\logic\api;

use app\common\logic\lib\Redis;
use app\common\logic\lib\Str;
use app\common\model\api\Friend as FriendModel;
use app\common\model\api\User as UserModel;
use Exception;
use WebSocket\Client;

class User
{
    private $userModel   = null;
    private $friendModel = null;
    private $str         = null;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->friendModel = new FriendModel();
        $this->str         = new Str();
        $this->redis       = new Redis();
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
        if (empty($user)) {
            throw new Exception('用户不存在！');
        }

        // 验证密码
        $salt     = $user['password_salt'];
        $password = md5($salt . $data['password'] . $salt);
        if ($password != $user['password']) {
            throw new Exception('密码错误');
        }

        // 删除上一次登录的token
        $this->redis->delete(config('redis.token_pre') . $user['last_login_token']);

        // 生成token
        $token = $this->str->createToken($user['username']);
        // 更新用户的登录信息
        $this->userModel->updateLoginInfo([
            'username'         => $user['username'],
            'last_login_token' => $token,
        ]);

        // 保存token，不设置过期时间
        $this->redis->set(config('redis.token_pre') . $token, [
            'id'       => $user['id'],
            'username' => $user['username'],
        ]);

        // 返回token
        return $token;
    }

    public function logout($token)
    {
        // 删除token
        $this->redis->delete($token);
    }

    public function addFriend($data)
    {
        // 找到要加为好友的用户
        $friend = $this->userModel->findByUserNameWithStatus($data['username']);
        if (empty($friend)) {
            throw new Exception('用户不存在！');
        }

        // 是否有重复申请
        $socket = $this->redis->get(config('redis.socket_pre') . $friend['id']);
        if (!empty($socket['apply_list'])) {
            foreach ($socket['apply_list'] as $key => $value) {
                if ($data['user']['id'] == $key) {
                    throw new Exception('请勿重复申请');
                }
            }
        }

        // 是否已经是好友
        if ($this->friendModel->isFriend($data['user']['id'], $friend['id'])) {
            throw new Exception('已成为好友！');
        }

        // 是否为黑名单
        $this->friendModel->isBlack($data['user']['id'], $friend['id']);

        // 不能加自己为好友
        if ($data['user']['id'] == $friend['id']) {
            throw new Exception('不能加自己为好友！');
        }
        
        // 加好友数据
        $send = [
            'type'     => 'addFriend',
            'uid'      => $data['user']['id'],
            'username' => $data['user']['username'],
            'target'   => $friend['id'],
            'message'  => $data['message'],
        ];
        $client = new Client('ws://124.71.218.160:9502?type=public&token=' . $data['token']);
        // $client = new Client('wss://124.71.218.160:9502?type=public&token=' . $data['token']);
        $client->send(json_encode($send));
        // 接收服务端返回的信息
        $receive = json_decode($client->receive(), true);
        if ($receive['status'] == config('statu.success')) {
            $client->close();
        }
    }

    // 处理加好友请求
    public function handleFriend($data)
    {
        // 查出自己的socket，里面的好友申请列表
        $socket = $this->redis->get(config('redis.socket_pre') . $data['uid']);
        // 对方的申请，在列表中是否有数据
        if (empty($socket['apply_list']) || !array_key_exists($data['target'], $socket['apply_list'])) {
            throw new Exception('该好友申请不存在！');
        }

        // 是否已经是好友，而已用户是否也向对方发出了好友申请
        if ($this->friendModel->isFriend($data['uid'], $data['target'])) {
            // 删除缓存，我的好友申请
            unset($socket['apply_list'][$data['target']]);
            $this->redis->set(config('redis.socket_pre') . $data['uid'], $socket);
            throw new Exception('对方已经是你的好友了！');
        }

        // 添加好友数据
        // 开启事务
        $this->friendModel->startTrans();
        try {
            // redis开启事务
            $this->redis->multi();
            if ((boolean) $data['decision']) {
                $lists = [
                    [
                        'uid' => $data['target'],
                        'fid' => $data['uid'],
                    ],
                    [
                        'uid' => $data['uid'],
                        'fid' => $data['target'],
                    ],
                ];
                $this->friendModel->saveAll($lists);
            }
            // 删除缓存的好友申请
            unset($socket['apply_list'][$data['target']]);
            $this->redis->drset(config('redis.socket_pre') . $data['uid'], $socket);

            // 提交事务
            $this->redis->exec();
            $this->friendModel->commit();
        } catch (Exception $e) {
            // 回滚事务
            $this->redis->discard();
            $this->friendModel->rollback();
            throw new Exception($e->getMessage());
        }
    }

    // 获取好友列表
    public function friendList($uid)
    {
        return $this->friendModel->friendList($uid);
    }
}
