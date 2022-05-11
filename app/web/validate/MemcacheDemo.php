<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class MemcacheDemo extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'phone|手机号码' => 'require|mobile',
    ];

    // 验证消息
    protected $message = [
        // 'phone.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'sendPhoneCode' => ['phone'],
    ];
}
