<?php

namespace app\api\controller;

use app\BaseController;
use app\common\logic\api\User as UserLogic;
use think\App;

class User extends BaseController
{
    protected $logic = '';

    public function __construct(App $app)
    {
        // 控制器初始化
        parent::__construct($app);
        $this->logic = new UserLogic();
    }

    // 注册
    public function register()
    {
        // 接收参数
        $params = $this->request->params;
        $this->logic->register($params);
        // 返回结果
        return $this->success('注册成功');
    }

    // 登录
    public function login()
    {
        // 接收参数
        $params = $this->request->params;
        $token  = $this->logic->login($params);
        // 返回结果
        return $this->success($token);
    }

    // 退出登录
    public function logout()
    {
        // 获取token
        $token = $this->getToken();
        // 删除token
        $this->logic->logout(config('redis.token_pre') . $token);
        // 返回结果
        return $this->success('退出登录成功！');
    }

    // 是否已登录，验证token
    public function isLogin()
    {
        return $this->success('token验证成功！');
    }

    // 加好友请求
    public function addFriend()
    {
        $data = $this->request->params;
        $data['user'] = $this->getUser();
        $data['token'] = $this->getToken();
        $this->logic->addFriend($data);
        return $this->success('好友申请已经发送！');
    }

    // 处理加好友请求
    public function handleFriend()
    {
        $data = $this->request->params;
        $data['uid'] = $this->getUid();
        $this->logic->handleFriend($data);
        return $this->success('好友申请处理完成！');
    }

    // 好友列表
    public function friendList()
    {
        $list = $this->logic->friendList($this->getUid());
        return $this->success($list);
    }
}
