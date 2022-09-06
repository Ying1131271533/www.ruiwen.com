<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class ElasticSearch extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'index'         => 'require',
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
        'index_list'             => [],
        'index_save'             => ['index'],
        // 'index' => ['id', 'page', 'size'],
        'read'             => ['id'],
        'save'             => ['user_id', 'article_id', 'content', 'nickname', 'status', 'parent_id'],
        'update'           => ['id', 'content', 'nickname'],
        'delete'           => ['id'],
    ];
}
