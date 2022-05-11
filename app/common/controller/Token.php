<?php
namespace app\common\controller;

use app\Request;

class Token
{
    public function get_token(Request $request)
    {
        $token = $request->buildToken('__token__', 'sha1');
        return success(['token' => $token]);
    }
}
