<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class RedisDemo extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'product_id|商品id' => 'require|number|gt:0',
        'user_id|用户id'    => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'id.gt' => '商品id必需大于0',
    ];

    // 验证场景
    protected $scene = [
        'dosecKill' => ['product_id', 'user_id'],
    ];
}
