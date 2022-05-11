<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Login extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'username|用户名' => 'require|chsDash',
        'password|密码'  => 'require',
        'code'         => 'require',
    ];

    // 验证消息
    protected $message = [
        'username.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'loginAccount'      => ['username', 'password'],
        'loginWechatApplet' => ['code'],
    ];
}
