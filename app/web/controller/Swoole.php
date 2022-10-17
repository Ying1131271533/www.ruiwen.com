<?php

namespace app\web\controller;
use \Swoole\Coroutine\Client;
class Swoole
{
    public function index()
    {
        // return view();
        return success('阿卡丽');
    }
}
