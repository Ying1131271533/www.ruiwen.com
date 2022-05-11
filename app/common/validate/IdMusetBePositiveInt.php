<?php

namespace app\common\validate;

class IdMusetBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];
}
