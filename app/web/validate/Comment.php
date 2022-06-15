<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Comment extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id'         => 'require|number|gt:0',
        'user_id'    => 'require|number|gt:0',
        'article_id' => 'require|number|gt:0',
        'content'    => 'require',
        'nickname'   => 'require',
        'likenum'    => 'require|number',
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
        'read'       => ['id'],
        'save'       => ['id', 'user_id', 'article_id', 'content', 'nickname', 'likenum', 'status'],
        'update'     => ['id', 'user_id', 'article_id', 'content', 'nickname', 'likenum', 'status'],
        'delete'     => ['id'],
        'getComment' => ['page', 'size'],
    ];
}
