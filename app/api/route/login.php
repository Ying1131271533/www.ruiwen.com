<?php

use think\facade\Route;


/**
 * 登录
 *
 */
Route::group('login', function () {
    // 登录
    Route::post('', 'login/index')->middleware(app\admin\middleware\Login::class);
    // 注册
    Route::post('register', 'login/register');
})->middleware(app\admin\middleware\Login::class);





















