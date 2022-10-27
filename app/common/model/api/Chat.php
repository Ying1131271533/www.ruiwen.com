<?php

namespace app\common\model\api;

use Exception;
use think\Model;

class Chat extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'api_chat';
    // 设置当前模型的数据库连接
    protected $connection = 'mysql';

    
}
