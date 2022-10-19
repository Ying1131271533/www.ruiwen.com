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
        halt(md5('AIGDKNGP8fga8f4IGHIBdaurcn123545fgpgsg123456'));
        // 接收参数
        $params = $this->request->params;
        $token = $this->logic->login($params);
        // 返回结果
        return $this->success($token);
    }
}
