<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class ElasticSearchDemo extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'index|索引'      => 'require|alpha',
        // 数据
        'id'            => 'require|number',
        'title|商品名称'    => 'require',
        'category|分类名称' => 'require',
        'price|商品价格'    => 'require|float',
        'images|图片地址'   => 'require',
        // 分页
        'page|页码'       => 'require|number|gt:0',
        'size|条数'       => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'index.require' => '用户名不能为空',
    ];

    // 验证场景
    protected $scene = [
        // 索引
        'index_save'   => ['index'],
        'index_read'   => ['index'],
        'index_delete' => ['index'],
        // 数据
        'read'         => ['id'],
        'save'         => ['id', 'title', 'category', 'price', 'images'],
        'update'       => ['id', 'title', 'category', 'price', 'images'],
        'delete'       => ['id'],
        // 搜索
        'search'       => ['page', 'size'],
    ];
}
