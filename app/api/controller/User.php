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
}
