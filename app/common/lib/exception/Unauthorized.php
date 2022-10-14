<?php
namespace app\common\lib\exception;

class Unauthorized extends BaseException
{
    public $msg    = '未经授权';
    public $code   = 401;
    public $status = 40001;
}
