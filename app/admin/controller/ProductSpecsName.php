<?php
namespace app\admin\controller;

use app\admin\logic\ProductSpecsName as PSNLogic;
use app\common\model\ProductSpecsName as PSNModel;
use app\lib\exception\Fail;
use app\Request;

class ProductSpecsName
{
    public function read($id)
    {
        $product_specs = PSNModel::with('specsValues')->find($id);
        if (!$product_specs) {
            throw new Fail('商品规格名称不存在');
        }
        return success($product_specs);
    }

    public function index(Request $request)
    {
        $list = PSNModel::getPageData($request->page, $request->size);
        return success($list);
    }

    public function save(Request $request)
    {
        $params = $request->params;
        $result = PSNModel::create($params);
        if (!$result) {
            return fail('保存失败');
        }
        return success($result['id'], 10001, 201);
    }

    public function update(Request $request)
    {
        $params = $request->params;
        $prod_cate = PSNModel::find($params['id']);
        if (empty($prod_cate)) {
            return fail('规格名称不存在');
        }
        $result = $prod_cate->save($params);
        if (!$result) {
            return fail('保存失败');
        }
        return success();
    }

    public function delete($id)
    {
        $result = PSNLogic::deleteById($id);
        if (!$result) return fail();
        return success();
    }
}
