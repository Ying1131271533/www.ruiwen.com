<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class User extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|用户id' => 'require',
    ];

    // 验证消息
    protected $message = [
        'username.id' => '用户id不能为空',
    ];

    // 验证场景
    protected $scene = [
        'saveInfo'  => ['nickName', 'gender', 'avatarUrl'],
    ];
}
