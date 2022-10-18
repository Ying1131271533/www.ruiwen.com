<?php
namespace app\common\lib\exception;

class Wechat extends BaseException
{
    public $msg    = '微信未知错误';
    public $HttpStatus   = 500;
    public $status = 50000;
}
