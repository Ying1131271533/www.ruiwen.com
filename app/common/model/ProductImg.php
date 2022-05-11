<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class ProductImg extends BaseModel
{
    use SoftDelete;
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
