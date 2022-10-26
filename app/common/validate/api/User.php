<?php

namespace app\common\validate\api;

use app\common\validate\BaseValidate;

class User extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|用户id'                    => 'require|number|gt:0',
        'username|用户名'               => 'require|max:20|min:2',
        'password|密码'                => 'require|max:50|min:6',
        'password_salt|密码盐'          => 'require',
        'last_login_token|上次登录Token' => 'require',
        'status|状态'                  => 'number',

        'message|加好友留言'              => 'max:20',

        'decision|接受状态'            => 'require|between:0,1',
        'target|对方id'                => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'id.require' => '用户id不能为空',
    ];

    // 验证场景
    protected $scene = [
        'register'  => ['username', 'password'],
        'login'     => ['username', 'password'],
        'addFriend' => ['username', 'message'],
        'handleFriend' => ['decision', 'target'],
    ];

    // edit 验证场景定义
    public function sceneRegister()
    {
        // 注册时添加username的唯一性
        return $this->only(['username', 'password'])->append('username', 'unique:api_user');
    }
}
