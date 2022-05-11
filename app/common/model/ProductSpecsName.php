<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class ProductSpecsName extends BaseModel
{
    use SoftDelete;

    public function specsValues()
    {
        return $this->hasMany(ProductSpecsValue::class);
    }
}
