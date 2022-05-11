<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class ProductSpecs extends BaseModel
{
    use SoftDelete;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 检查商品库存
    public static function checkStockById($id, $number)
    {
        $res = self::where('id', $id)->where('stock', '>=', $number)->findOrEmpty()->isEmpty();
        return $res;
    }
}
