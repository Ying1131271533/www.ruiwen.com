<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class User extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|用户id'      => 'require',
        'username|用户名' => 'require|max:20|min:2',
        'password|密码'  => 'require|max:50|min:6',
        'phone|手机'     => 'require|mobile',
    ];

    // 验证消息
    protected $message = [
        'username.id' => '用户id不能为空',
    ];

    // 验证场景
    protected $scene = [
        'saveInfo' => ['nickName', 'gender', 'avatarUrl'],
        'register' => ['username', 'password', 'phone'],
        // 虽然Login从User分开了.....
        'login'    => ['username', 'password'],
    ];

    // edit 验证场景定义
    public function sceneRegister()
    {
        // 注册时添加username的唯一性
    	return $this->only(['username', 'password', 'phone'])->append('username', 'unique:user');
    }
}
