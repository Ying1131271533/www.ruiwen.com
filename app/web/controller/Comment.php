<?php
namespace app\web\controller;

use app\common\mongo\Comment as MongoComment;
use app\common\mongo\Info;
use app\common\mongo\User;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use app\lib\exception\Params;
use app\Request;
use app\web\logic\Mongo as LogicMongo;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteConcern;

class Comment
{
    public function index(Request $request)
    {
        $comment = MongoComment::select();
        return success($comment);
    }

    public function read(Request $request)
    {
        $id      = (int) $request->params['id'];
        $comment = MongoComment::findCommentById($id);
        return success($comment);
    }

    public function save(Request $request)
    {
        // 组装数据
        $data = [
            'id'          => $request->param('id/d'),
            'user_id'     => $request->param('user_id/d'),
            'article_id'  => $request->param('article_id/d'),
            'content'     => $request->param('content/s'),
            'nickname'    => $request->param('nickname/s'),
            'likenum'     => $request->param('likenum/d'),
            'status'      => $request->param('status/d'),
            'create_time' => time(),
        ];

        $user = MongoComment::insert($data);
        if (!$user) {
            throw new \Exception('评论失败');
        }

        return success();
    }

    public function update(Request $request)
    {
        // 组装数据
        $data = [
            'id'          => $request->param('id/d'),
            'user_id'     => $request->param('user_id/d'),
            'article_id'  => $request->param('article_id/d'),
            'content'     => $request->param('content/s'),
            'nickname'    => $request->param('nickname/s'),
            'likenum'     => $request->param('likenum/d'),
            'status'      => $request->param('status/d'),
            'create_time' => time(),
        ];

        $manager = new Manager("mongodb://localhost:27017");
        $buck    = new BulkWrite(['ordered' => true]);

        // $buck->update(['id'=>$data['id']], ['$set'=>$data], ['multi'=>true]);
        $buck->update(['id' => $data['id']], ['$set' => $data]);

        $wirte = new WriteConcern(WriteConcern::MAJORITY, 1000);

        $result = $manager->executeBulkWrite('www_ruiwen_com.comment', $buck, $wirte);
        if (!$result) {
            throw new \Exception('评论失败');
        }

        return success();
    }

    public function delete(Request $request)
    {
        $id   = $request->param('id/d');
        $user = User::where('id', $id)->find();
        if (!$user) {
            throw new Miss();
        }

        $result = $user->delete();
        if (!$result) {
            throw new Fail();
        }

        return success('删除成功');
    }

    public function getComment(Request $request)
    {
        $page    = $request->param('page/d');
        $size    = $request->param('size/d');
        $comment = MongoComment::getPageData($page, $size);
        return success($comment);
    }

    // 文章点赞
    public function like(Request $request)
    {
        $manager = new Manager("mongodb://localhost:27017");
        $buck    = new BulkWrite(['ordered' => true]);

        // 文章点赞加一
        $id = $request->param('id/d');
        $buck->update(['id' => $id], ['$inc' => ['likenum' => 1]]);

        $wirte  = new WriteConcern(WriteConcern::MAJORITY, 1000);
        $result = $manager->executeBulkWrite('www_ruiwen_com.comment', $buck, $wirte);
    }
}
