<?php

namespace app\web\controller;

use app\common\model\ProductSpecsValue;
use app\Request;
use app\web\logic\Product as LogicProduct;

class Product
{
    public function getBasicInfo(Request $request)
    {
        $params = $request->params;
        $product = LogicProduct::getBasicInfoById($params['id']);
        return success($product);
    }
}
