<?php

use think\facade\Route;

/**
 * rabbit_test
 */
Route::group('rabbitmq_test', function () {
    Route::get('index', 'RabbitmqTest/index');
    // 生产者
    Route::post('publisher', 'RabbitmqTest/publisher');
    // 工作队列
    Route::post('work', 'RabbitmqTest/work');
    // 广播
    Route::post('fanout', 'RabbitmqTest/fanout');
    // 订阅模型 - 直连模式
    Route::post('direct', 'RabbitmqTest/direct');
    // 订阅模型 - Topic 动态路由
    Route::post('topic', 'RabbitmqTest/topic');
    // 生产者 发布确认
    Route::post('publisher_confirm', 'RabbitmqTest/publisher_confirm');
    // 死信队列
    Route::post('dead', 'RabbitmqTest/dead');
    // 延迟队列
    Route::post('delay', 'RabbitmqTest/delay');
    // 延迟队列优化 - 单个消息延迟
    Route::post('delay_optimization', 'RabbitmqTest/delay_optimization');
    // 延迟队列优化 - 延时队列插件
    Route::post('delayed', 'RabbitmqTest/delayed');
    // 生产者 发布确认 高级 消息回退
    Route::post('confirm_high', 'RabbitmqTest/confirm_high');
    // 生产者 发布确认 高级 备用交换机
    Route::post('confirm_backup', 'RabbitmqTest/confirm_backup');
    // 优先级队列
    Route::post('priority_queue', 'RabbitmqTest/priority_queue');
    // 惰性队列
    Route::post('lazy_queue', 'RabbitmqTest/lazy_queue');
    // 测试镜像集群
    Route::post('mirror', 'RabbitmqTest/mirror');
    // 测试federation
    Route::post('federation', 'RabbitmqTest/federation');
    // 测试shovel
    Route::post('shovel', 'RabbitmqTest/shovel');
});
