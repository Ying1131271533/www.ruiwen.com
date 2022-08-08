<?php

namespace app\web\validate;

use app\common\validate\BaseValidate;

class RabbitmqTest extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'msg|消息'          => 'require',
        // 固定的
        'routing_key|路由键' => 'require',
        // 变化的
        'route_key|路由'    => 'require',
    ];

    // 验证消息
    protected $message = [
        'msg.require'         => '消息不能为空',
        'routing_key.require' => '路由键不能为空',
        'route_key.require'   => '路由不能为空',
    ];

    // 验证场景
    protected $scene = [
        'publisher'         => ['msg'],
        // 发布确认
        'publisher_confirm' => ['msg'],
        'work'              => ['msg'],
        'fanout'            => ['msg'],
        'direct'            => ['msg', 'routing_key'],
        'topic'             => ['msg', 'route_key'],
        // 死信队列
        'publisher_dead'    => ['msg', 'routing_key'],
    ];
}
