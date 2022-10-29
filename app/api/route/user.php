<?php

use think\facade\Route;

/**
 * 用户
 *
 */
Route::group('user', function () {
    // 注册
    Route::rule('register', 'user/register', 'POST');
    // 登录
    Route::rule('login', 'user/login', 'POST');
});

// 视图
Route::group('View/user', function () {
    // 注册
    Route::rule('register', '/api/View/register', 'GET');
    // 登录
    Route::rule('login', '/api/View/login', 'GET');
    // 首页
    Route::rule('index', '/api/View/index', 'GET');
});

// 需要已登录的操作
Route::group('user', function () {
    // 获取用户
    Route::rule('getUserByToken', 'user/getUserByToken', 'POST');
    // 获取用户
    Route::rule('getUserById', 'user/getUserById', 'POST');
    // 好友列表
    Route::rule('friendList', 'user/friendList', 'POST');
    // 处理加好友请求
    Route::rule('handleFriend', 'user/handleFriend', 'POST');
    // 加好友
    Route::rule('addFriend', 'user/addFriend', 'POST');
    // 退出登录
    Route::rule('logout', 'user/logout', 'POST');
    // 是否已登录，验证token
    Route::rule('isLogin', 'user/isLogin', 'POST');
})->middleware(app\api\middleware\IsLogin::class);
