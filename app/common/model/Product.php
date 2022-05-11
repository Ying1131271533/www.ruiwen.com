<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class Product extends BaseModel
{
    use SoftDelete;

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function specs()
    {
        return $this->hasMany(ProductSpecs::class);
    }

    public function imgs()
    {
        return $this->hasMany(ProductImg::class);
    }

    // 获取商品和关联数据
    public static function getProductById(int $id)
    {
        $product = self::with(['imgs', 'specs'])
            ->withCache(dawn_time($id))
            ->cache(dawn_time($id))
            ->find($id);
        return $product;
    }
}
