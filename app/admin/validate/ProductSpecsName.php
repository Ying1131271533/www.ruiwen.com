<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;

class ProductSpecsName extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|规格名称id' => 'require|number',
        'name|规格名称' => 'require|unique:product_specs_name|max:25',
        'sort|规格排序' => 'require|number|lt:30000',
    ];

    // 验证消息
    protected $message = [
        'name.unique' => '规格名称已存在',
    ];

    // 验证场景
    protected $scene = [
        'save'   => ['name', 'sort'],
        'update' => ['id', 'name','sort'],
        'read'   => ['id'],
        'delete' => ['id'],
    ];
}
