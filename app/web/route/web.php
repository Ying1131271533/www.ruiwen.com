<?php

use think\facade\Route;

/**
 * Home
 */
Route::get('', 'home/index');
Route::get('home/akali', 'home/akali');
Route::get('home/ying', 'home/ying');
Route::get('home/jinx', 'home/jinx');
Route::get('home/shell', 'home/shell');

/**
 * 商品
 */
Route::group('product', function () {
    Route::get('info/:id', 'product/getBasicInfo'); // 获取商品基础信息
})->middleware([app\web\middleware\Product::class]);

/**
 * 登录
 */
Route::group('login', function () {
    // 用户登录
    Route::post('login_account', 'login/loginAccount')->middleware(app\web\middleware\Login::class);
    // 微信登录
    Route::get('loginWechatApplet', 'login/loginWechatApplet')->middleware(app\web\middleware\WechatAppletLogin::class);
});

/**
 * 用户
 */
Route::group('user', function () {
    Route::post('save_info', 'User/saveInfo');
})->middleware(app\web\middleware\Auth::class);

/**
 * RedisTest
 */
Route::group('redis_test', function () {
    Route::get('string', 'RedisTest/string');
    Route::get('list', 'RedisTest/list');
    Route::get('set', 'RedisTest/set');
    Route::get('zset', 'RedisTest/zset');
    Route::get('hash', 'RedisTest/hash');
    Route::get('hash_slot', 'RedisTest/hashSlot');
})->middleware([app\common\middleware\RedisClose::class]);

/**
 * RedisDome
 */
Route::group('redis', function () {
    Route::post('dosec_kill', 'RedisDemo/dosecKill')->middleware([app\web\middleware\DosecKill::class]);
    Route::post('sentinel', 'RedisDemo/sentinel');
})->middleware([app\common\middleware\RedisClose::class]);

/**
 * 验证码
 */
Route::group('code', function () {
    Route::get('get_code', 'code/getCode');
})->middleware([app\common\middleware\RedisClose::class]);

/**
 * 购物车
 * only只要四个方法，其它的不要，避免被访问
 */
Route::resource('cart', 'Cart')->vars(['cart' => 'spec_id'])->only(['index', 'read', 'update', 'save'])->middleware([app\web\middleware\Auth::class, app\web\middleware\Cart::class]);

/**
 * MemcacheTest
 */
Route::group('memcache_test', function () {
    Route::get('index', 'MemcacheTest/index');
    Route::get('set', 'MemcacheTest/set');
    Route::get('fenbu', 'MemcacheTest/fenbu');
    Route::get('hash', 'MemcacheTest/hash');
})->middleware([app\common\middleware\MemcacheClose::class]);

/**
 * MemcacheDemo
 */
Route::group('memcache_demo', function () {
    Route::get('online_members', 'MemcacheDemo/onlineMembers');
    Route::get('top', 'MemcacheDemo/top');
    Route::get('send_phone_code', 'MemcacheDemo/sendPhoneCode');
})->middleware([app\common\middleware\MemcacheClose::class]);

/**
 * Docker
 */
Route::group('docker', function () {
    Route::get('index', 'Docker/index');
});

/**
 * Mongo
 */
Route::group('mongo', function () {
    Route::get('', 'Mongo/index');
    Route::get(':id', 'Mongo/read');
    Route::post('', 'Mongo/save');
    Route::put('', 'Mongo/update');
    Route::delete(':id', 'Mongo/delete');
});

/**
 * Comment 评论
 */
Route::group('comment', function () {
    Route::get('', 'Comment/index');
    Route::get('getParentComment', 'Comment/getParentComment');
    Route::get(':id', 'Comment/read');
    Route::post('', 'Comment/save');
    Route::put('', 'Comment/update');
    Route::delete(':id', 'Comment/delete');
    Route::get('like/:id', 'Comment/like');
});

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

/**
 * elastic_searech
 */
Route::group('elastic_search', function () {
    // 索引
    Route::post('index_save', 'ElasticSearch/index_save');
    Route::get('index_list', 'ElasticSearch/index_list');
    Route::get('index_read/:index', 'ElasticSearch/index_read');
    Route::delete('index_delete/:index', 'ElasticSearch/index_delete');
    // 数据
    Route::get('read/:id', 'ElasticSearch/read');
    Route::get('', 'ElasticSearch/index');
    Route::post('', 'ElasticSearch/save');
    Route::put('', 'ElasticSearch/update');
    Route::delete(':id', 'ElasticSearch/delete');
    Route::post('bulk_save', 'ElasticSearch/bulk_save');
    Route::post('bulk_update', 'ElasticSearch/bulk_update');
    Route::post('bulk_delete', 'ElasticSearch/bulk_delete');
    Route::post('search', 'ElasticSearch/search');
});

/**
 * elastic_search_demo
 */
Route::group('elastic_search_demo', function () {
    // 索引
    Route::post('index_save', 'ElasticSearchDemo/index_save');
    Route::get('index_read/:index', 'ElasticSearchDemo/index_read');
    Route::delete('index_delete/:index', 'ElasticSearchDemo/index_delete');
    // 数据
    Route::get(':id', 'ElasticSearchDemo/read');
    Route::get('', 'ElasticSearchDemo/index');
    Route::post('', 'ElasticSearchDemo/save');
    Route::put('', 'ElasticSearchDemo/update');
    Route::delete(':id', 'ElasticSearchDemo/delete');
    Route::post('bulk_save', 'ElasticSearchDemo/bulk_save');
    Route::post('bulk_update', 'ElasticSearchDemo/bulk_update');
    Route::post('bulk_delete', 'ElasticSearchDemo/bulk_delete');
    Route::post('search', 'ElasticSearchDemo/search');
});
