<?php
namespace app\web\controller;

use app\common\mongo\BaseMongo;
use app\common\mongo\Comment as MongoComment;
use app\common\mongo\Info;
use app\common\mongo\User;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use app\lib\exception\Params;
use app\Request;
use app\web\logic\Mongo as LogicMongo;
use think\db\builder\Mongo as BuilderMongo;
use think\db\Mongo;
use think\db\connector\Mongo as MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;

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
        
        
        /* 
        
        $manager = new Manager('mongodb://127.0.0.1:27017');
        $cmd = [
            // 集合名
            'update' => 'comment',
            'indexes' => [
                [
                    // 索引名
                    'name' => 'user_id',
                    // 索引字段数组
                    'key' => ['user_id' => 1],
                ]
            ]
        ];
        $command = new Command($cmd);
        $res = $manager->executeCommand('www_ruiwen_com', $command);
        halt($res); */

        $user = MongoComment::where('id', $data['id'])->inc('likenum');
        // $user = MongoComment::where('id', $data['id'])->update($data);
        if (!$user) {
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
}
