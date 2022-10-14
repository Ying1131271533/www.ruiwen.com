<?php
namespace app\admin\logic;

use app\common\model\ProductSpecsName as PSNModel;
use app\common\lib\exception\Fail;
use app\common\lib\exception\Miss;

class ProductSpecsName
{
    public static function deleteById($id)
    {
        // 找到规格名称
        $specs_name = PSNModel::find($id);
        if (!$specs_name) throw new Miss('规格不存在');

        // 规格名称下面是否有规格值
        $specs_values = $specs_name->specsValues;
        if ($specs_values->isEmpty()) {
            $result = $specs_name->delete();
            if (!$result) {
                throw new Fail('规格删除失败');
            }
        } else {
            throw new Fail('规格下有规格值，不能删除');
        }
        return true;
    }
}
