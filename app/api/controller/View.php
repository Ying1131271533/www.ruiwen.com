<?php

namespace app\api\controller;

use app\BaseController;
use think\facade\View as FacadeView;

class View extends BaseController
{
    public function index()
    {
        return FacadeView::fetch('index/index');
    }

    public function register()
    {
        return FacadeView::fetch('index/register');
    }

    public function login()
    {
        return FacadeView::fetch('index/login');
    }
}
