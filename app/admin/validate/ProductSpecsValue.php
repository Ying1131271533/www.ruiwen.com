<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;

class ProductSpecsValue extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|规格值id'                     => 'require|number',
        'product_specs_name_id|规格名称id' => 'require|number',
        'value|规格值名称'                  => 'require|max:25|unique:product_specs_value',
    ];

    // 验证消息
    protected $message = [
        'name.unique' => '规格值已存在',
    ];

    // 验证场景
    protected $scene = [
        'read'   => ['id'],
        'save'   => ['product_specs_name_id', 'value', 'specs_name'],
        'update' => ['id', 'product_specs_name_id', 'value', 'specs_name'],
        'delete' => ['id'],
    ];
}
