<?php

namespace app\common\validate\api;

use app\common\validate\BaseValidate;

class Chat extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'fid|对方的id' => 'require|number|gt:0',
    ];

    // 验证场景
    protected $scene = [
        'record' => ['fid'],
    ];
}
