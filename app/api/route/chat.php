<?php

use think\facade\Route;

/**
 * 聊天
 *
 */
Route::group('chat', function () {
    // 聊天室测试
    Route::rule('test', 'Chat/test', 'POST');
    // 聊天室
    Route::rule('room', 'Chat/room', 'POST');
})->middleware(app\api\middleware\IsLogin::class);

// 视图
Route::group('View/chat', function () {
    // 聊天室测试
    Route::rule('test', '/api/View/chat_test', 'GET');
    // 聊天室
    Route::rule('room', '/api/View/chat_room', 'GET');
});