<?php
namespace app\common\lib\exception;

use think\Exception;

abstract class BaseException extends Exception
{
    public $msg        = '';
    public $errorCode  = 200;
    public $httpStatus = 200;
    public $data       = null;

    public function __construct($params = [])
    {
        if (is_string($params) && (int) ($params) == 0) {
            $this->msg = $params;
            return;
        }

        if (!is_array($params) || empty($params)) {
            return;
        }

        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }

        if (array_key_exists('status', $params)) {
            $this->status = $params['status'];
        }

        if (array_key_exists('data', $params)) {
            $this->data = $params['data'];
        }
    }
}
