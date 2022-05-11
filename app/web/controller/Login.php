<?php
namespace app\web\controller;

use app\Request;
use app\web\logic\Login as LogicLogin;

class Login
{
    public function loginAccount(Request $request)
    {
        $user_id = $request->user_id;
        $token   = LogicLogin::loginAccount($user_id);
        return success(['token' => $token]);
    }

    public function loginWechatApplet(Request $request)
    {
        $code  = $request->params['code'];
        $token = (new LogicLogin)->loginWechatApplet($code);
        return success(['token' => $token]);
    }
}
