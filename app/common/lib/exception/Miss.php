<?php
namespace app\common\lib\exception;

class Miss extends BaseException
{
    public $msg    = '未找到数据';
    public $HttpStatus   = 404;
    public $status = 40004;
}
