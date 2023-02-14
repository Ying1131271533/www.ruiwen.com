<?php

namespace app\common\lib\login;

// 登录工厂
class ClassAttr
{
    private $classStats;
    public function __construct()
    {
        $this->classStats = [
            // 帐号登录 这个UP没做
            'login' => 'app\common\lib\login\LoginAccount',
            // 微信
            'wx' => 'app\common\lib\login\WechatApplet',
        ];
    }

    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/02/26 15:57
     *
     * @param  string    $type                类名
     * @param  array     $params              参数
     * @param  bool      $needInstance        是否需要实例化
     * @return object/string                  返回对象/类名
     */
    public function initClass($type, $params = [], $needInstance = true)
    {
        $class = $this->classStats;
        if (!array_key_exists($type, $class)) {
            return false;
        }
        // 赋值类名
        $className = $class[$type];
        // 把对象/类名传递过去
        return $needInstance == true ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }
}
