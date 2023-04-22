<?php
namespace app\web\logic;

use app\common\model\Product;
use app\common\model\ProductSpecs;
use app\common\lib\exception\Miss;
use Error;
use think\facade\Cache;

class Cart
{
    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/02/28 16:44
     *
     * 添加购物车到redis中
     * 1、检查库存
     * 2、加入redis中，判断如果有+num，如果没有，就新建一个
     *      添加进去的数据格式
     *      [
     *          'name' => '商品名',
     *          'image' => '商品图',
     *          'specs' => '颜色:红色,尺寸:32',
     *          'number' => '商品数量',
     *          'create_time' => '创建时间',
     *      ]
     * 
     * @param  int      $spec_id    规格id
     * @param  int      $number		数量
     * @param  string   $type       操作类型
     * @return json                 api返回的json数据
     */
    public static function editToCartRedis($spec_id, $number, $type = 'save')
    {
        $noStock = ProductSpecs::checkStockById($spec_id, $number);
        if ($noStock) {
            throw new Miss('库存不足');
        }
        $user_id = request()->user_id;
        $cache = Cache::HGET('mall_cart_'. $user_id, $spec_id);
        if ($cache) {
            $cartData = json_decode($cache, true);
            $type == 'save' ? $cartData['number'] += $number : $cartData['number'] = $number;
        }else{
            $product_spec = ProductSpecs::field('specs_value_id, product_id')->find($spec_id);
            $prodcut = Product::field('name, image')->find($product_spec['prodcut_id']);
            $data = [
                'spec_id' => $spec_id, // 自己加的，应该有一个商品规格的id
                'name' => $prodcut['name'],
                'image' => $prodcut['image'],
                'specs' => '',
                'number' => $number,
                'create_time' => time(),
            ];
            $data = json_encode($data);
            $result = Cache::store('redis')->set('mall_cart_'. $user_id, $data);
            if($result){
                throw new Error('添加失败');
            }
        }

    }
}
