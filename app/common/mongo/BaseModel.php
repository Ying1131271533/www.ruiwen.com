<?php
namespace app\common\mongo;

use think\model;

abstract class BaseModel extends model
{
    protected $connection = 'mongo';
    
    public static function getPageData(int $page, int $size, string $order = 'id')
    {
        return self::order($order, 'desc')->paginate($size, false, ['page' => $page]);
    }

}
