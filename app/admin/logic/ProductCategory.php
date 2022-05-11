<?php
namespace app\admin\logic;

use app\common\model\ProductCategory as PCModel;
use app\lib\exception\Fail;

class ProductCategory
{
    public static function deleteById($id)
    {
        // 找到分类
        $product_category = PCModel::find($id);
        if (!$product_category) {
            throw new Fail('分类不存在');
        }

        // 分类下面是否有商品
        $products = $product_category->products;
        if ($products->isEmpty()) {
            $result = $product_category->delete();
            if (!$result) {
                throw new Fail('分类删除失败');
            }
        } else {
            throw new Fail('分类下有商品，不能删除');
        }
    }
}
