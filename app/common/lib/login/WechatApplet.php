<?php
namespace app\common\lib\login;

use app\common\lib\exception\Error;
use app\common\lib\exception\Fail;
use app\common\lib\exception\Success;
use app\common\lib\exception\Wechat;

class WechatApplet
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wechat.app_id');
        $this->wxAppSecret = config('wechat.app_secret');
        $this->wxLoginUrl = sprintf(config('wechat.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get(){
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        
        if (empty($wxResult)) {
            throw new Error('获取session_key及openid时异常，微信内部错误');
        }else{
            $loginFail = array_key_exists('errorcode', $wxResult);
            if ($loginFail) {
                throw new Wechat();
            }else{
                return $wxResult;
            }
        }
    }
}
