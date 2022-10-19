<?php

namespace app\common\validate\api;

use app\common\validate\BaseValidate;

class User extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|用户id'                    => 'require|number|gt:0',
        'username|用户名'               => 'require|unique:api_user|max:20|min:2',
        'password|密码'                => 'require|max:50|min:6',
        'password_salt|密码盐'          => 'require',
        'last_login_token|上次登录Token' => 'require',
        'status|状态'                  => 'number',
    ];

    // 验证消息
    protected $message = [
        'id.require' => '用户id不能为空',
    ];

    // 验证场景
    protected $scene = [
        'register' => ['username', 'password'],
        'login' => ['username', 'password'],
    ];

    // edit 验证场景定义
    public function sceneLogin()
    {
        // 登录时移除username的唯一性
    	return $this->remove('username', 'unique');
    }
}
