<?php
namespace app\web\controller;

use app\common\mongo\Comment as MongoComment;
use app\common\mongo\User;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use app\lib\exception\Params;
use app\Request;
use app\web\logic\Comment as LogicComment;

class Comment
{

    public function index(Request $request)
    {
        $comment = MongoComment::select();
        return success($comment);
    }

    public function read(int $id)
    {
        // cmd命令
        $comment = (new LogicComment)->findCommentById($id);
        return success($comment);

        // 模型
        $comment = MongoComment::findCommentById($id);
        return success($comment);
    }

    public function save(Request $request)
    {
        (new LogicComment)->saveComment($request);
        return success();
    }

    public function update(Request $request)
    {
        (new LogicComment())->updateComment($request);
        return success();
    }

    public function delete(int $id)
    {
        (new LogicComment())->deleteCommentById($id);
        return success('删除成功');
    }

    // 根据上级id查询文章评论的分页列表
    public function getParentComment(Request $request)
    {
        $id      = $request->param('id/d');
        $page    = $request->param('page/d');
        $size    = $request->param('size/d');
        $comment = (new LogicComment)->getCommentListByParentId($id, $page, $size);
        return success($comment);
    }

    // 文章点赞
    public function like(Request $request)
    {
        $id = $request->param('id/d');
        (new LogicComment())->addLike($id);
        return success();
    }
}
