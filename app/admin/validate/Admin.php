<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;

class admin extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|用户名id'     => 'require|number',
        'username|用户名' => 'require|admin.unique',
        'password|密码'  => 'require|length:32',
    ];

    // 验证消息
    protected $message = [
        'username.unique' => '该昵称已被使用！',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['id'],
        'register' => ['username', 'password', 'code'],
    ];

    // bind_email 验证场景定义
    public function sceneBind_email()
    {
        return $this->only(['id', 'code', 'email'])->append('email', 'unique:user');
    }
}
