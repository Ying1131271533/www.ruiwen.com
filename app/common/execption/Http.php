<?php

namespace app\common\execption;

use think\exception\Handle; // 异常处理接管
use think\Response;

class Http extends Handle
{
    private $msg    = null;
    private $status = null;

    // 异常处理接管
    public function render($request, \Throwable $e): Response
    {
        $this->msg    = $e->getMessage();
        $this->status = config('status.failed');
        if ($this->status == config('status.goto')) {
            $this->status = config('sataus.goto');
        }
        return json([
            'status'  => $this->status,
            'message' => $this->msg,
            'result'  => null,
        ]);
    }
}
