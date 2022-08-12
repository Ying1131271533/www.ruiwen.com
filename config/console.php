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
        // 练习
        'consumer'          => 'app\common\command\Consumer',
        'consumer_confirm'  => 'app\common\command\ConsumerConfirm',
        'publisher_confirm' => 'app\common\command\PublisherConfirm',
        'work'              => 'app\common\command\Work',
        'fanout'            => 'app\common\command\Fanout',
        'direct'            => 'app\common\command\Direct',
        'topic'             => 'app\common\command\Topic',
        // 死信 练习
        'normal'            => 'app\common\command\Normal',
        'dead'              => 'app\common\command\Dead',
        // 延迟队列
        'delay'              => 'app\common\command\Delay',
        // 延迟队列 - 插件
        'delayed'              => 'app\common\command\Delayed',
        // 正式
        'simple_queue'      => 'app\common\command\SimpleQueue',
        'work_queue'        => 'app\common\command\WorkQueue',
        'fanout_queue'      => 'app\common\command\FanoutQueue',
        'direct_queue'      => 'app\common\command\DirectQueue',
        'topic_queue'       => 'app\common\command\TopicQueue',
    ],
];
