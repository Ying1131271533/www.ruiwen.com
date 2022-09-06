<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,

    // 签名，签名由参数变成字符串组合起来的，可以参照微信的接口
    'sign'             => 'Akali',
    // jwt密钥
    'token_key'        => 'U2OpdWDyzQ4iSUWaCAaXaGg3qEzR00Qv3fwMkkWKQ5CXjIWLJTmg8g==',

    // 页码
    'page'             => 1,
    // 条数
    'size'             => 10,

    // redis的服务器群
    'redis_server'     => ['127.0.0.1:6381', '127.0.0.1:6382', '127.0.0.1:6383'],
    // memcache的服务器群
    'memcache_server'  => ['127.0.0.1:11212', '127.0.0.1:11213', '127.0.0.1:11214'],

    // redis连接配置
    'redis'            => [
        'host'     => '127.0.0.1',
        // 'host' => 'redis',
        'password' => 'Ym-12]i4!gDal^Jc/3@n.c^Mh',
        'port'     => 6379,
    ],

    // memcache连接配置
    'memcache'         => [
        'host'     => 'memcache',
        'password' => '',
        'port'     => 1121,
    ],

    // rabbitmq连接配置
    'rabbitmq'         => [
        // 'host'     => '124.71.218.160',
        'host'     => '127.0.0.1',
        // 'host'     => '192.168.0.184',
        // 'host'     => 'rabbitmq',
        'port'     => 5672,
        // 'port'     => 5673,
        // 'port'     => 5674,
        'login'    => 'akali',
        'password' => '123456',
        'vhost'    => '/',
        // 'vhost'    => '/akali',
    ],

    // elasticsearch连接配置
    'elasticsearch'    => [
        'http' => ['http://127.0.0.1:9200'],
        // 如果采用了HTTP认证
        // 'http' => ['user:pass@http://127.0.0.1:9200'],
        // SSL
        // 'https' => ['user:pass@https://127.0.0.1:9200'],
    ],
];
