<?php
namespace app\admin\controller;

use app\lib\exception\Success;
use app\admin\logic\Login as LoginLogic;
use app\Request;

class Login
{
    public function index(Request $request)
    {
        $admin  = $request->admin;
        $token = LoginLogic::getToken($admin);
        throw new Success(['data' => $token]);
    }
}
