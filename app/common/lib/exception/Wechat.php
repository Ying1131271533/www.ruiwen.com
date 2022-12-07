<?php
namespace app\common\lib\exception;

class Wechat extends BaseException
{
    public $msg    = '微信未知错误';
    public $errorCode   = 500;
    public $httpStatus = 500;
}
