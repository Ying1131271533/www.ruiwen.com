<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class ProductCategory extends BaseModel
{
    use SoftDelete; // 软删除

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
