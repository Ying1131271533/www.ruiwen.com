<?php
namespace app\common\lib\exception;

class Success extends BaseException
{
    public $msg    = '成功';
    public $HttpStatus   = 200;
    public $status = 20000;
}
