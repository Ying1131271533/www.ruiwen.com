<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class ElasticSearch extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id'       => 'require|number',
        'index|索引' => 'require|alpha',
        // 分页
        'page|页码'  => 'require|number|gt:0',
        'size|条数'  => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'name.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'index_save'   => ['index'],
        'index_read'   => ['index'],
        'index_delete' => ['index'],
        // 分页
        // 'index' => ['id', 'page', 'size'],
        'read'         => ['id'],
        // 'save'         => ['id'],
        // 'update'       => ['id'],
        'delete'       => ['id'],
    ];
}
