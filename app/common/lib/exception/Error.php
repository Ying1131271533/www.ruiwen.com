<?php
namespace app\common\lib\exception;

// 好像不需要，程序出错就油由 系统自带的 \Exception 来处理
class Error extends BaseException
{
    public $msg    = '服务发生错误';
    public $code   = 500;
    public $status = 50000;
}
