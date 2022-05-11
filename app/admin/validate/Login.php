<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;

class Login extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'username|用户名'      => 'require|chsDash',
        'password|密码'       => 'require|length:32',
        'code|手机验证码'        => 'require|number|length:6',
        'verify_code|图片验证码' => 'require|length:4|alpha',
    ];

    // 验证消息
    protected $message = [
        'username.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'index'    => ['username', 'password', 'verify_code'],
        'register' => ['username', 'password', 'code'],
    ];

}
