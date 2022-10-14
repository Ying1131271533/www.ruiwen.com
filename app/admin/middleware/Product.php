<?php

namespace app\admin\middleware;

use app\common\model\ProductCategory;
use app\common\model\ProductSpecsValue;
use app\common\lib\exception\Fail;
use app\common\lib\exception\Miss;
use app\common\lib\exception\Params;

/**
 * 商品中间件
 *
 * @param Request     $request      请求对象
 * @param Closure     $next         中间件对象
 * @return return                   $next($request);
 */
class Product
{
    public function handle($request, \Closure $next)
    {
        // 检验保存和更新的方法传递的参数
        if($request->action() == 'save' || $request->action() == 'update') {
            self::checkCateId($request->params['product_category_id']);
            self::checkSpecsValue($request->params['specs'], $request->params['spec_type']);
        }
        
        return $next($request);
    }

    /**
     * 检验产品分类
     *
     * @param int     $cate_id      分类id
     * @return return               throw
     */
    private static function checkCateId($cate_id)
    {
        $result = ProductCategory::find($cate_id);
        if (!$result) {
            throw new Miss('产品分类不存在');
        }

    }

    /**
     * 校验产品的规格
     *
     * @param array     $specs      规格数据
     * @return return               throw
     */
    private static function checkSpecsValue(array $specs_arr, int $sepc_type)
    {
        // 1:校验每组产品规格值ID是否相同
        $specs_value_id_arr = array_column($specs_arr, 'specs_value_id');
        if($sepc_type == 1){
            foreach ($specs_value_id_arr as &$value) {
                $value = explode(',', $value);
                sort($value);
                $value = implode(',', $value);
            }
        }
        
        if (count(array_column($specs_arr, 'specs_value_id')) != count(array_unique($specs_value_id_arr))) {
            throw new Params('商品规格值重复');
        }

        // 2:校验产品规格值ID是否存在
        foreach ($specs_value_id_arr as &$value) {
            if ($sepc_type == 1) {
                self::checkMultiSpecsValue($value);
            }else{
                if (strpos($value, ',')) throw new Params('请选择商品为多规格');
                self::checkSingleSpecsValue($value);
            }
        }
    }

    /**
     * 单规格校验
     * 
     * @param arintray     $id      规格值id
     * @return return               throw
     */
    private static function checkSingleSpecsValue(int $id)
    {
        $specs_value = ProductSpecsValue::find($id);
        if(!$specs_value) throw new Miss('产品规格值不存在');
    }

    /**
     * 多规格校验
     * 
     * @param arintray     $id      规格值id
     * @return return               throw
     */
    private static function checkMultiSpecsValue(string $ids)
    {
        // 2:校验产品规格值ID是否存在
        // 3:校验产品规格值字符串是否包含相同ID
        $specs_value_id_arr = explode(',', $ids);
        $specs_value_list = ProductSpecsValue::whereIn('id', $ids)->select()->toArray();
        if (count($specs_value_id_arr) != count($specs_value_list)) {
            throw new Params('产品规格值id错误');
        }
        // 4:校验产品规格值ID是否出现相同规格名，例如:颜色:红颜色:蓝
        $specs_value_name_id_arr = array_column($specs_value_list, 'product_specs_name_id');
        if (count($specs_value_id_arr) != count(array_unique($specs_value_name_id_arr))) {
            throw new Params('产品规格值有重复id');
        }
    }

}
