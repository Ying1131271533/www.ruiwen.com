<?php
namespace app\web\logic;

use app\common\model\Product as ModelProduct;
use app\lib\exception\Miss;

class Product
{
    public static function getBasicInfoById($id)
    {
        $product = ModelProduct::with(['specs' => function($query){
            // $query -> field(['id','specs_value_id','price','stock','product_id']);
            $query -> withoutField(['sales']);
        }])->withCache(dawn_time($id))->find($id);

        if (!$product) throw new Miss();

        $product = $product->toArray();
        // $product = ModelProduct::find($id);
        // $product['specs'] = ProductSpecs::getSpecsByProdId($id);
        $product['show_specs'] = ProductSpecs::getShowSpecs($product['specs']);
        // halt($product);

        return $product;
    }
}
