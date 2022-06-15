<?php
namespace app\common\mongo;

class Comment extends BaseMongo
{
    public static function findCommentById(int $id){
        $comment = self::find($id);
        return $comment;
    }
}
