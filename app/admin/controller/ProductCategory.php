<?php
namespace app\admin\controller;

use app\admin\logic\ProductCategory as PCLogic;
use app\common\model\ProductCategory as PC;
use app\lib\exception\Fail;
use app\Request;

class ProductCategory
{
    public function index(Request $request)
    {
        $list = PC::getPageData($request->page, $request->size);
        return success($list);
    }

    public function save(Request $request)
    {
        $params = $request->params;
        $product = PC::create($params);
        if (!$product) {
            return fail('保存失败');
        }
        return success($product['id'], 10001, 201);
    }

    public function update(Request $request)
    {
        // 获取参数
        $params = $request->params;

        // 查询数据库
        $prod_cate = PC::find($params['id']);
        if (empty($prod_cate)) {
            return fail('id为' . $params['id'] . '的分类不存在');
        }

        // 修改数据库
        $result = $prod_cate->save($params);
        if (!$result) {
            return fail('保存失败');
        }
        return success($prod_cate);
    }

    public function delete($id)
    {
        PCLogic::deleteById($id);
        return success(['id' => $id]);
    }

    public function read($id)
    {
        $prod_cate = PC::find($id);
        // halt($prod_cate->getData()); // 获取被隐藏的字段
        if (!$prod_cate) {
            throw new Fail('分类不存在');
        }
        return success($prod_cate);
    }
}
