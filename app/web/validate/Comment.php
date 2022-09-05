<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Comment extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id'            => 'require|alphaDash',
        'user_id'    => 'require|alphaDash',
        'article_id' => 'require|alphaDash',
        'content'    => 'require',
        'nickname'   => 'require',
        'status'     => 'in:1,2',
        // 分页
        'page|页码'    => 'require|number|gt:0',
        'size|条数'    => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'name.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'read'             => ['id'],
        'save'             => ['id', 'user_id', 'article_id', 'content', 'nickname', 'status', 'parent_id'],
        'update'           => ['id', 'content', 'nickname'],
        'delete'           => ['id'],
        'getParentComment' => ['id', 'page', 'size'],
        'like'             => ['id'],
    ];
}
