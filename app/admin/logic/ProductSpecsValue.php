<?php
namespace app\admin\logic;

use app\common\model\ProductSpecsValue as PSVModel;
use app\common\model\ProductSpecsName as PSNModel;
use app\lib\exception\Fail;
use app\lib\exception\Miss;

class ProductSpecsValue
{
    public static function saveSpecsValue(array $params)
    {
        // 找到规格名称
        $prodcut_name = PSNModel::find($params['product_specs_name_id']);
        if(!$prodcut_name) throw new Miss('规格名称不存在');
        
        $specs_value = $prodcut_name->specsValues()->save($params);
        if ($specs_value->isEmpty()) throw new Fail('保存失败');

        return $specs_value;
    }

    public static function deleteById($id)
    {
        $specs_value = PSVModel::find($id);
        if (!$specs_value) throw new Miss('规格值不存在');

        $result = $specs_value->delete();
        if (!$result) throw new Fail('删除失败');
    }
}
