<?php
namespace app\admin\controller;

use app\admin\logic\Product as LogicProduct;
use app\common\model\Product as ProductModel;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use app\Request;
use app\web\logic\ProductSpecs;

class Product
{
    public function index(Request $request)
    {
        $list = ProductModel::getPageData($request->page, $request->size);
        return success($list);
    }

    public function read(int $id)
    {
        // halt(date('Y-m-d H:i:s', time() + dawn_time($id)));
        $product = ProductModel::getProductById($id);
        if(!$product) throw new Miss();
        $product['show_specs'] = ProductSpecs::getShowSpecs($product['specs']->toArray());
        return success($product);
    }

    public function save(Request $request)
    {
        $params  = $request->params;
        $product = LogicProduct::saveProduct($params);
        return create($product);
    }

    public function update(Request $request)
    {
        $params  = $request->params;
        $product = LogicProduct::saveProduct($params);
        return success($product);
    }

    public function delete(int $id)
    {
        $result = LogicProduct::deleteById($id);
        if($result !== true) return fail($result);
        return success();
    }
}
