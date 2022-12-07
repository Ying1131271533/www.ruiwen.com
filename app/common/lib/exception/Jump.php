<?php
namespace app\common\lib\exception;

class Jump extends BaseException
{
    public $msg        = '跳转';
    public $errorCode  = 101;
    public $httpStatus = 101;
}
