<?php

namespace app\common\model;

use think\model;

// use think\model\concern\SoftDelete;

abstract class BaseModel extends model
{
    // 软删除
    // use SoftDelete;
    // protected $deleteTime = 'delete_time';
    // 开启自动写入时间戳字段，自动写入create_time和update_time两个字段的值
    // protected $autoWriteTimestamp = true;

    // 隐藏字段
    protected $hidden = [
        'passowrd',
        'create_time',
        'update_time',
        'delete_time',
    ];
    // 获取被隐藏数据
    // halt($user->getData());
    // 获取被隐藏的字段
    // halt($user->getData('name'));

    // 获取有分页参数的数据 - 接口用
    // public static function getPageData(int $page, int $size, string $order = 'id')
    public static function getPageData(int $page = 1, int $size = 30, string $order = 'id')
    {
        return self::order($order, 'desc')->paginate($size, false, ['page' => $page]);
    }
    
    // 获取渲染分页
    public static function getPageList(array $where = [], int $limit = 30, array $order = ['id' => 'desc'])
    {
        return self::where($where)->order($order)->paginate($limit);
    }
    // 获取数据分页
    public static function getListData(array $where = [], array $order = ['id' => 'desc'], $limit = false)
    {
        return self::where($where)->limit($limit)->order($order)->select();
    }
}
