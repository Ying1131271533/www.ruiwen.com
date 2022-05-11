<?php
namespace app\web\logic;

use app\common\model\ProductSpecs as ModelProductSpecs;
use app\common\model\ProductSpecsName;
use app\common\model\ProductSpecsValue;
use app\lib\exception\Miss;

class ProductSpecs
{
    public static function getSpecsByProdId($product_id)
    {
        $product_specs = ModelProductSpecs::where('product_id', $product_id)
            ->field('id,specs_value_id,price,stock')
            ->select()
            ->toArray();
        return $product_specs;
    }

    public static function getShowSpecsAkali($product_specs)
    {
        // 组合商品各组规格值id 用逗号隔开 1,4,5 + 1,2,7 = 1,4,5,1,2,7
        $specs_ids      = implode(',', array_column($product_specs, 'specs_value_id'));
        $specs_name_ids = ProductSpecsValue::whereIn('id', $specs_ids)->column('product_specs_name_id');
        $specs_name_ids = array_unique($specs_name_ids);

        // 获取商品拥有的规格
        $list = ProductSpecsName::select($specs_name_ids);
        foreach ($list as $key => $specs_name) {
            // 获取用户关联的profile模型数据
            $list[$key]['specs'] = $specs_name
                ->specsValues()
                ->whereIn('id', $specs_ids)
                ->field('id,value')
                ->select();
        }
        return $list->toArray();
    }

    public static function getShowSpecs($product_specs)
    {
        // 组合商品规格值id 用逗号隔开
        $specs_ids  = implode(',', array_column($product_specs, 'specs_value_id'));
        $specs      = ProductSpecsValue::whereIn('id', $specs_ids)->select()->toArray();
        $specs_name = array_unique(array_column($specs, 'specs_name'));
        $show_specs = [];
        foreach ($specs_name as $name) {
            $specs_belong_name = array_filter_value($specs, 'specs_name', $name);
            array_push($show_specs, ['specs_name' => $name, 'specs' => $specs_belong_name]);
        }
        return $show_specs;
    }
}
