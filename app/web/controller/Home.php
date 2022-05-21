<?php

namespace app\web\controller;

use app\common\model\Akali;

class Home
{
    public function index()
    {
        return success('神织知更');
    }

    public function akali()
    {
        return success('阿卡丽');
    }

    public function ying()
    {
        return success('樱之节');
    }

    public function jinx()
    {
        return success('金克丝');
    }

    // 脚本尝试：curl 124.71.218.160/web/home/shell
    public function shell()
    {
        $result = Akali::create(['name' => '神织知更']);
        // return success($result);
        // if(!$result) $this->shell();
    }
}
