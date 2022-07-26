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
        'consumer'     => 'app\common\command\Consumer',
        'work'         => 'app\common\command\Work',
        'fanout'       => 'app\common\command\Fanout',
        'direct'       => 'app\common\command\Direct',
        // 正式
        'simple_work'  => 'app\common\command\SimpleWork',
        'work_queue'   => 'app\common\command\WorkQueue',
        'send_queue'   => 'app\common\command\SendQueue',
        'direct_queue' => 'app\common\command\DirectQueue',
        'topic_queue'  => 'app\common\command\TopicQueue',
    ],
];
