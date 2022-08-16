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
        // 过期时间
        'ttl|过期时间'        => 'require',
    ];

    // 验证消息
    protected $message = [
        'msg.require'         => '消息不能为空',
        'routing_key.require' => '路由键不能为空',
        'route_key.require'   => '路由不能为空',
        'ttl.require'         => '过期时间不能为空',
    ];

    // 验证场景
    protected $scene = [
        'publisher'          => ['msg'],
        'work'               => ['msg'],
        'fanout'             => ['msg'],
        'direct'             => ['msg', 'routing_key'],
        'topic'              => ['msg', 'route_key'],
        // 发布确认
        'publisher_confirm'  => ['msg'],
        // 死信队列
        'dead'               => ['msg', 'routing_key'],
        // 延迟队列
        'delay'              => ['msg', 'routing_key'],
        // 延迟队列优化 - 单个消息延迟
        'delay_optimization' => ['msg', 'ttl'],
        // 延迟队列 - 延时队列插件
        'delayed'            => ['msg', 'ttl'],
        // 发布确认 - 高级
        'confirm_high'       => ['msg', 'routing_key'],
        // 发布确认 - 高级 备用交换机
        'confirm_backup'     => ['msg'],
        // 优先队列
        'priority_queue'     => ['msg'],
        // 惰性队列
        'lazy_queue'         => ['msg'],
        // 测试镜像集群
        'mirror'             => ['msg'],
    ];
}
