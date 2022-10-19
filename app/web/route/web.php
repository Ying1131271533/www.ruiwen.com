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
 * swoole
 */
Route::group('swoole', function () {
    // index
    Route::get('index', 'Swoole/index');
});
