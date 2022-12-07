<?php
namespace app\common\lib\exception;

class Token extends BaseException
{
    public $msg    = 'Toten已过期或无效';
    public $errorCode   = 403;
    public $httpStatus = 403;
}
