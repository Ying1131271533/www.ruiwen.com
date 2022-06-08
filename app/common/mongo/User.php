<?php
namespace app\common\mongo;

class User extends BaseModel
{
    public function info()
    {
        return $this->hasOne(Info::class, 'user_id', 'id');
    }
}
