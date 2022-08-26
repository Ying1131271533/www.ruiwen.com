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
    // halt($user->getData()); // 获取被隐藏的字段

    public static function getPageData(int $page, int $size, string $order = 'id')
    {
        return self::order($order, 'desc')->paginate($size, false, ['page' => $page]);
    }
}
