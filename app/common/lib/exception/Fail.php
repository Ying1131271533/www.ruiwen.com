<?php
namespace app\common\lib\exception;

class Fail extends BaseException
{
    public $msg    = '失败';
    public $code   = 400;
    public $status = 40000;
}
