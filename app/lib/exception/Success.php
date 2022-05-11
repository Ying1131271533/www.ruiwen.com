<?php
namespace app\lib\exception;

class Success extends BaseException
{
    public $msg    = '成功';
    public $code   = 200;
    public $status = 20000;
}
