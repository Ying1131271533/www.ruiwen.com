<?php

use think\facade\Route;

/**
 * elastic_searech
 */
Route::group('elastic_search', function () {
    // 索引
    Route::post('index_save', 'ElasticSearch/index_save');
    Route::get('index_list', 'ElasticSearch/index_list');
    Route::get('index_read/:index', 'ElasticSearch/index_read');
    Route::put('index_update', 'ElasticSearch/index_update');
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
    // 8.x的异步客户端
    Route::post('async_client', 'ElasticSearch/async_client');
});

/**
 * elastic_search_test
 */
Route::group('elastic_search_test', function () {
    // 索引
    Route::post('index_save', 'ElasticSearchTest/index_save');
    Route::get('index_read/:index', 'ElasticSearchTest/index_read');
    Route::put('index_update', 'ElasticSearch/index_update');
    Route::delete('index_delete/:index', 'ElasticSearchTest/index_delete');
    // 数据
    Route::get(':id', 'ElasticSearchTest/read');
    Route::get('', 'ElasticSearchTest/index');
    Route::post('', 'ElasticSearchTest/save');
    Route::put('', 'ElasticSearchTest/update');
    Route::delete(':id', 'ElasticSearchTest/delete');
    Route::post('bulk_save', 'ElasticSearchTest/bulk_save');
    Route::post('bulk_update', 'ElasticSearchTest/bulk_update');
    Route::post('bulk_delete', 'ElasticSearchTest/bulk_delete');
    Route::post('search', 'ElasticSearchTest/search');
});
