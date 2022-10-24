<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动

    'default' => env('cache.driver', 'redis'),

    // 缓存连接方式配置
    'stores'  => [
        // redis
        'redis'    => [
            // 驱动方式
            'type'     => 'redis',
            // 服务器地址
            'host'     => config('app.redis.host'),
            // 'host'     => '127.0.0.1',
            // 'host'     => 'redis',
            // 密码.
            'password' => config('app.redis.password'),
            // 端口
            'port'     => config('app.redis.port'),
            // 库
            'select'   => 5,
            // 缓存有效期 0表示永久缓存
            'expire'   => null,
            // 'expire'   => cache_time('one_month'),
        ],
    ],
];
