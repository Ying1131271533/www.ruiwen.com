<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Mongo extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id'            => 'require|alphaDash',
        'name|用户名'      => 'require|chsDash',
        'age|年龄'        => 'require|number',
        'gender|性别'     => 'require',
        'profession|职业' => 'require',
    ];

    // 验证消息
    protected $message = [
        'name.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'read'   => ['id'],
        'save'   => ['id', 'name', 'age', 'gender', 'profession'],
        'update' => ['id', 'name', 'age', 'gender'],
        'delete' => ['id'],
    ];
}
