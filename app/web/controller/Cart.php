<?php

namespace app\web\controller;

use app\Request;
use app\web\logic\Cart as cartLogic;

class Cart
{
    public function index()
    {
        return success('神织知更');
    }

    public function save(Request $requst)
    {
        $params = $requst->params;
        $result = cartLogic::editToCartRedis($params['spec_id'], $params['number']);
        return success('添加成功');
    }
}
