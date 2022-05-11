<?php
namespace app\admin\controller;

use app\admin\logic\ProductSpecsValue as PSVLogic;
use app\common\model\ProductSpecsValue as PSVModel;
use app\lib\exception\Fail;
use app\Request;

class ProductSpecsValue
{
    public function index(Request $request)
    {
        $list = PSVModel::getPageData($request->page, $request->size);
        return success($list);
    }

    public function save(Request $request)
    {
        $params = $request->params;
        $specs_value = PSVLogic::saveSpecsValue($params);
        return success($specs_value['id'], 10001, 201);
    }

    public function update(Request $request)
    {
        $params = $request->params;
        $specs_value = PSVLogic::saveSpecsValue($params);
        return success();
    }

    public function delete($id)
    {
        PSVLogic::deleteById($id);
        return success();
    }

    public function read($id)
    {
        $prod_cate = PSVModel::find($id);
        if (!$prod_cate) throw new Fail('规格值不存在');
        return success($prod_cate);
    }
}
