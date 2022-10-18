<?php
namespace app\common\lib\exception;

class Forbidden extends BaseException
{
    public $msg    = '没有权限';
    public $HttpStatus   = 403;
    public $status = 40003;
}
