<?php
namespace app\common\lib\exception;

class Params extends BaseException
{
    public $msg    = '参数错误';
    public $code   = 300;
    public $status = 30000;
}
