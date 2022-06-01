<?php
namespace app\common\mongo;

use think\Model;

class User extends Model
{
    protected $connection = 'mongo';
}