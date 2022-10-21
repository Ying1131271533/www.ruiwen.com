<?php

namespace app\api\controller;

use app\BaseController;
use think\facade\View as FacadeView;

class View extends BaseController
{
    public function index()
    {
        return FacadeView::fetch('user/index');
    }

    public function register()
    {
        return FacadeView::fetch('user/register');
    }

    public function login()
    {
        return FacadeView::fetch('user/login');
    }
    
    // 聊天室测试
    public function chat_test()
    {
        return FacadeView::fetch('chat/test');
    }

    // 聊天室
    public function chat_room()
    {
        return FacadeView::fetch('chat/room');
    }
}
