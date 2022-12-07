<?php
namespace app\common\lib\exception;

class Success extends BaseException
{
    public $msg    = '成功';
    public $errorCode   = 200;
    public $httpStatus = 200;
}
