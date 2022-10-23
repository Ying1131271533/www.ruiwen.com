<?php

namespace app\common\model\api;

use think\Model;

class Friend extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'api_friend';
    // 设置当前模型的数据库连接
    protected $connection = 'mysql';

    
}
