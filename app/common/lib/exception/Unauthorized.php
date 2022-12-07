<?php
namespace app\common\lib\exception;

class Unauthorized extends BaseException
{
    public $msg    = '未经授权';
    public $errorCode   = 401;
    public $httpStatus = 401;
}
