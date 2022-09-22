<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class ElasticSearch extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'index|索引'     => 'require|alpha',
        // 数据
        'id'           => 'require',
        'username|用户名' => 'require',
        'age|年龄'       => 'require|number',
        'sex|性别'       => 'require|chs',
        // 分页
        'page|页码'      => 'require|number|gt:0',
        'size|条数'      => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'name.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        'index_save'   => ['index'],
        'index_read'   => ['index'],
        'index_update'   => ['index'],
        'index_delete' => ['index'],
        // 分页
        'read'         => ['id'],
        // 'save'         => ['id'],
        'update'       => ['id', 'username', 'age', 'sex'],
        'delete'       => ['id'],
        // 搜索
        'search'   => ['index', 'page', 'size'],
    ];
}
