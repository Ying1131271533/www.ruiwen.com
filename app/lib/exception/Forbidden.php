<?php
namespace app\lib\exception;

class Forbidden extends BaseException
{
    public $msg    = '没有权限';
    public $code   = 403;
    public $status = 40003;
}
