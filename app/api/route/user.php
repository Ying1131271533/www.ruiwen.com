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
    Route::rule('index', '/api/View/index', 'GET');
});
// 需要已登录的操作
Route::group('user', function () {
    // 退出登录
    Route::rule('logout', 'user/logout', 'POST');
    // 是否已登录，验证token
    Route::rule('isLogin', 'user/isLogin', 'POST');
})->middleware(app\api\middleware\IsLogin::class);
