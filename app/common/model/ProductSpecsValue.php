<?php

namespace app\common\model;

class ProductSpecsValue extends BaseModel
{
    public function specsName()
    {
        return $this->belongsTo(ProductSpecsName::class);
    }
}
