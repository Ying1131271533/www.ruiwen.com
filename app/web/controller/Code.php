<?php

namespace app\web\controller;

use app\lib\exception\Error;
use app\lib\exception\Fail;
use app\lib\exception\Success;
use phpmailer\PHPMailer;
use think\cache\driver\Redis;

class Code
{
    // Redis对象
    protected static $redis;
    public function __construct()
    {
        self::$redis = new Redis(['host' => '124.71.218.160', 'password' => 'Ak12al&iYi4%3*@n.!g']);
    }

    /**
     * 获取手机验证码
     *
     * @param  string    $name      角色名称
     * @param  int       $code      验证码
     * @return json                 api返回的json数据
     */
    public static function getCode(string $phone)
    {
        // 用户获取验证码
        self::sendCode($phone);

        // 验证用户提交的验证码
        // $code = '723751';
        // self::verifyCode($phone, $code);
        throw new Success('验证成功');
    }

    /**
     * 检测手机验证码
     *
     * @return
     */
    public static function verifyCode(String $phone, String $code)
    {
        $redis = self::$redis;
        // 验证码key
        $codeKey   = "verifyCode-" . $phone . ':code';
        $redisCode = $redis->get($codeKey);
        if (!$redisCode) {
            throw new Fail('请重新发送验证码');
        }
        
        // 判断验证码是否一致
        if ($redisCode === $code) {
            $redis->del($codeKey);
        } else {
            throw new Fail('验证码错误');
        }
    }

    /**
     * 发送手机验证码
     * 检测手机号码发送短信次数，每天只能发送3次，验证码放到redis缓存，设置过期时间
     *
     * @return
     */
    public static function sendCode(String $phone)
    {
        $redis = self::$redis;
        // 手机发送次数key
        $countKey = 'verify_code:' . $phone . ':count';
        // 验证码key
        $codeKey = 'verify_code:' . $phone . ':code';

        // 获取发送次数
        $count = $redis->get($countKey);
        // 发送短信次数
        if (!$count) {
            // 获取当天剩余时间
            $over_time = cache_time('over_day');
            // 使用redis的setex()命令，设置过期时间为今天剩余时间，发送次数为1
            $result = $redis->setex($countKey, $over_time, 1);
            if (!$result) {
                throw new Error();
            }

        } else {
            // 每个手机每天只能发送三次
            if ((int) $count < 3) {
                // 发送次数+1
                $result = $redis->incr($countKey);
                if (!$result) {
                    throw new Error();
                }

            } else {
                // 发送三次，不能再发送
                throw new Fail('今天发送短信次数已经超过三次了');
            }
        }

        // 获取手机验证码，直接生成，暂时不去服务商那里获取
        $code = get_random_number();
        // 手机验证码缓存到redis里面
        $result = $redis->setex($codeKey, 300, $code);
        if (!$result) {
            throw new Error();
        }

        return $code;
    }
}
