<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class Cart extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'spec_id|规格'  => 'require|number|gt:0',
        'spec_ids|规格' => 'require|isCommaString',
        'number|数量'   => 'require|number|gt:0',
    ];

    // 验证消息
    protected $message = [
        'spec_id.require' => '请选择规格',
    ];

    // 验证场景
    protected $scene = [
        'save'   => ['spec_id', 'number'],
        'delete' => ['spec_ids'],
        'update' => ['spec_id', 'number'],
        'read'   => ['spec_id'],
    ];
}
