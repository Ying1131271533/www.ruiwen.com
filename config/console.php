<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    // 使用方法是在命令行输入：
    // php think simple_work
    // php think simple_work --option msg=akali
    // 未定义的话则是 php think common/command/simple_work --option msg=akali
    'commands' => [
        // 普通
        'consumer'          => 'app\common\command\Consumer',
        // 工作队列
        'work'              => 'app\common\command\Work',
        // 广播
        'fanout'            => 'app\common\command\Fanout',
        // 直连
        'direct'            => 'app\common\command\Direct',
        // 主题
        'topic'             => 'app\common\command\Topic',
        // 发布确认
        'publisher_confirm' => 'app\common\command\PublisherConfirm',
        'consumer_confirm'  => 'app\common\command\ConsumerConfirm',
        // 死信 练习
        'normal'            => 'app\common\command\Normal',
        'dead'              => 'app\common\command\Dead',
        // 延迟队列
        'delay'             => 'app\common\command\Delay',
        // 延迟队列 - 插件
        'delayed'           => 'app\common\command\Delayed',
        
        // 发布确认 - 高级
        'confirm_high'      => 'app\common\command\ConfirmHigh',
        // 发布确认 - 高级 备份交换机
        'confirm_backup'      => 'app\common\command\ConfirmBackup',
        // 发布确认 - 高级 备份交换机 警告
        'confirm_warning'      => 'app\common\command\ConfirmWarning',

        // 优先级队列
        'priority_queue'      => 'app\common\command\PriorityQueue',

        // 测试镜像集群
        'mirror'      => 'app\common\command\Mirror',
        // 测试federation
        'federation'      => 'app\common\command\Federation',
        // 测试shovel
        'shovel'      => 'app\common\command\Shovel',
    ],
];
