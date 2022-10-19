<?php

use think\facade\Route;

/**
 * 用户
 *
 */
Route::group('user', function () {
    // 注册
    Route::post('register', 'user/register');
    // 登录
    Route::post('login', 'user/login');
});
