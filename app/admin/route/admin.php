<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
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

/**
 * 管理员
 *
 */
Route::group('admin', function () {
     // 获取管理员信息
    Route::post('info', 'admin/info')->middleware(app\admin\middleware\Auth::class)->allowCrossDomain();
});
























