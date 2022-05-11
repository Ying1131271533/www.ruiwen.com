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

// 获取密钥
Route::get('crypt/get_key', 'crypt/get_key');
// 获取表单令牌
Route::get('token/get_token', 'token/get_token');


// ajax改版数据状态
Route::get('ajax/change_status', 'Ajax/changeStatus');

// 上传单张图片
Route::post('upload/file', 'Upload/file');


