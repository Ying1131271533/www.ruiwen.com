<?php
namespace app\common\mongo;

class User extends BaseMongo
{
    public function info()
    {
        return $this->hasOne(Info::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public static function findUserById(int $id){
        
    }
}
