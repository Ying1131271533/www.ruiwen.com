<?php
namespace app\common\lib\exception;

class Miss extends BaseException
{
    public $msg    = '未找到数据';
    public $errorCode   = 404;
    public $httpStatus = 404;
}
