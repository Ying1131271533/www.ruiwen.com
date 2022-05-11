<?php
namespace app\admin\logic;

use app\common\model\Product as ProductModel;
use app\lib\exception\Fail;
use app\lib\exception\Miss;

class Product
{
    /**
     * 保存商品
     * 包括一般信息和规格信息，规格字段为$params['specs']
     *
     * @param  array    $params     商品数据
     * @return json                 api返回的json数据
     */
    public static function saveProduct(array $params)
    {
        // halt($params);
        $product = new ProductModel();
        $id = isset($params['id']) ? $params['id'] : null;
        if ($id) {
            $product = $product->find($id);
            if (empty($product)) throw new Miss('找不到该商品');
            
            // 获取旧图片
            // $old_image = $product['image'];
            // $old_images = $product->imgs()->column('path');
        }
        // halt($product);

        // 启动事务
        $product->startTrans();
        
        try {
            // 删除原来的规格、图册
            if ($id) {
                $productSpecsDel = $product->specs->delete();
                if (!$productSpecsDel) throw new \Exception('原来的商品规格删除失败');
                $productImgsDel = $product->imgs->delete();
                if (!$productImgsDel) throw new \Exception('原来的商品图册删除失败');
            }

            // 保存新的商品规格
            $stock     = 0;
            $specs_arr = $params['specs'];
            foreach ($specs_arr as &$specs) {
                $stock += $specs['stock'];
                $productSpecsResult = $product->specs()->save($specs);
                if (!$productSpecsResult) throw new \Exception('商品规格保存失败');
            }

            // 保存商品图片
            $imgs = $params['imgs'];
            foreach ($imgs as $value) {
                $productImgsResult = $product->imgs()->save(['path' => $value]);
                if (!$productImgsResult) throw new \Exception('商品图册保存失败');
            }

            // 保存商品信息，这里不能在删除关联数据上面，不然delete()不能使用
            $productResult = $product->save($params);
            if (!$productResult) throw new \Exception('商品保存失败');

            // 赋值商品总库存
            $product->stock = $stock;
            $stockSave      = $product->save();
            if (!$stockSave) throw new \Exception('商品总库存保存失败');

            // 提交事务
            $product->commit();
            
            // 删除旧图片，如果有软删除的话，那这里就不能用了
            // if($id){
            //     del_old_img($old_image, $params['image']);
            //     del_old_imgs($old_images, $params['images']);
            // }

            $product->specs;
            $product->imgs;
            return $product;
        } catch (\Exception $e) {
            $product->rollback();
            throw new Fail($e->getMessage());
        }

    }

    public static function deleteById(int $id)
    {
        $product = ProductModel::with(['specs', 'imgs'])->find($id);
        if(!$product) throw new Miss();

        // 开启事务
        $product->startTrans();
        try {
            $result = $product->together(['specs', 'imgs'])->delete();
            if(!$result){
                $product->rollback();
                throw new \Exception('商品删除失败');
            }
            
            $product->commit();
            return $result;
        } catch (\Exception $e) {
            $product->rollback();
            throw new Fail($e->getMessage());
        }
    }
}
