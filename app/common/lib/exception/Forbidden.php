<?php
namespace app\common\lib\exception;

class Forbidden extends BaseException
{
    public $msg    = '没有权限';
    public $errorCode   = 403;
    public $httpStatus = 403;
}
