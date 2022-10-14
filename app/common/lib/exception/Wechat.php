<?php
namespace app\common\lib\exception;

class Wechat extends BaseException
{
    public $msg    = '微信未知错误';
    public $code   = 500;
    public $status = 50000;
}
