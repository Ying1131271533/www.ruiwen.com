<?php

namespace app\web\middleware;

class WechatAppletLogin
{
    public function handle($request, \Closure $next)
    {
        
        return $next($request);
    }
}
