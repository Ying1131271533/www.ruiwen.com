<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class PhoneCode extends BaseValidate
{
    // 验证规则
    protected $rule = [
        // 'username|用户名' => 'require|chsDash',
    ];

    // 验证消息
    protected $message = [
        'username.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'user'              => ['username', 'password'],
        'loginWechatApplet' => ['code'],
    ];
}
