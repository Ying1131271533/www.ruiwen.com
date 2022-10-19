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
 * 商品
 *
 */
// 商品分类
Route::resource('prod_cate', 'ProductCategory')->middleware(app\admin\middleware\ProductCategory::class);
// 商品规格名称
Route::resource('prod_specs_name', 'ProductSpecsName')->middleware(app\admin\middleware\ProductSpecsName::class);
// 商品规格名称值
Route::resource('prod_specs_value', 'ProductSpecsValue')->middleware(app\admin\middleware\ProductSpecsValue::class);
// 商品
// Route::resource('product', 'Product')->middleware(app\admin\middleware\Product::class);
Route::group('product', function () {
    Route::get(':id', 'product/read');
    Route::get('', 'product/index');
    Route::post('', 'product/save');
    Route::put('', 'product/update');
    Route::delete(':id', 'product/delete');
})->middleware(app\admin\middleware\Product::class);























