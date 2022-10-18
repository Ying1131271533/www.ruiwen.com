<?php
namespace app\common\lib\exception;

class Token extends BaseException
{
    public $msg    = 'Toten已过期或无效';
    public $HttpStatus   = 403;
    public $status = 40003;
}
