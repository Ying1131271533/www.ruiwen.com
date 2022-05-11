<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Product extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|商品id' => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'id.lt'    => '商品id必需大于0',
    ];

    // 验证场景
    protected $scene = [
        'getBasicInfo'   => ['id'],
    ];
}