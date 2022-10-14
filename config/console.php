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

        /***************** swoole *****************/

        'tcp'               => 'app\common\command\swoole\TCP',
        'tcp_client'        => 'app\common\command\swoole\TCPClient',

        
        /***************** rabbitmq *****************/

        // 普通
        'consumer'          => 'app\common\command\rabbitmq\Consumer',
        // 工作队列
        'work'              => 'app\common\command\rabbitmq\Work',
        // 广播
        'fanout'            => 'app\common\command\rabbitmq\Fanout',
        // 直连
        'direct'            => 'app\common\command\rabbitmq\Direct',
        // 主题
        'topic'             => 'app\common\command\rabbitmq\Topic',
        // 发布确认
        'publisher_confirm' => 'app\common\command\rabbitmq\PublisherConfirm',
        'consumer_confirm'  => 'app\common\command\rabbitmq\ConsumerConfirm',
        // 死信 练习
        'normal'            => 'app\common\command\rabbitmq\Normal',
        'dead'              => 'app\common\command\rabbitmq\Dead',
        // 延迟队列
        'delay'             => 'app\common\command\rabbitmq\Delay',
        // 延迟队列 - 插件
        'delayed'           => 'app\common\command\rabbitmq\Delayed',

        // 发布确认 - 高级
        'confirm_high'      => 'app\common\command\rabbitmq\ConfirmHigh',
        // 发布确认 - 高级 备份交换机
        'confirm_backup'    => 'app\common\command\rabbitmq\ConfirmBackup',
        // 发布确认 - 高级 备份交换机 警告
        'confirm_warning'   => 'app\common\command\rabbitmq\ConfirmWarning',

        // 优先级队列
        'priority_queue'    => 'app\common\command\rabbitmq\PriorityQueue',

        // 测试镜像集群
        'mirror'            => 'app\common\command\rabbitmq\Mirror',
        // 测试federation
        'federation'        => 'app\common\command\rabbitmq\Federation',
        // 测试shovel
        'shovel'            => 'app\common\command\rabbitmq\Shovel',
    ],
];
