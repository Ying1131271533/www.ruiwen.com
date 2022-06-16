<?php
namespace app\web\logic;

use app\common\mongo\Comment as MongoComment;
use app\lib\exception\Fail;
use app\lib\exception\Miss;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\WriteConcern;

class Comment
{
    protected $manager;
    protected $buck;
    protected $wirte;
    
    public function __construct()
    {
        $this->manager = new Manager("mongodb://localhost:27017");
        $this->buck    = new BulkWrite(['ordered' => true]);
        $this->wirte   = new WriteConcern(WriteConcern::MAJORITY, 1000);
    }

    // 运行mongo命令
    protected function manager($buck, $wirte)
    {
        $result = $this->manager->executeBulkWrite('www_ruiwen_com.comment', $buck, $wirte);
        return $result;
    }

    /**
     * 根据父级id找到下级所有评论
     *
     * @param  int      $paren_id   父级id
     * @param  int      $page       页码
     * @param  int      $size       分页条数
     * @return array                评论数据
     */
    public function getCommentListByParentId(int $paren_id, int $page, int $size)
    {
        // 找到评论
        $comment = MongoComment::find($paren_id);
        if (!$comment) {
            throw new Miss('该评论不存在');
        }

        // 找到回复评论数据
        $commentList = MongoComment::where('parent_id', $paren_id)
            ->order('id', 'desc')
            ->paginate($size, false, ['page' => $page]);

        return $commentList;
    }
    
    public function saveComment($request)
    {
        // 组装数据
        $data = [
            'id'          => $request->param('id/d'),
            'user_id'     => $request->param('user_id/d'),
            'article_id'  => $request->param('article_id/d'),
            'content'     => $request->param('content/s'),
            'nickname'    => $request->param('nickname/s'),
            'likenum'     => 0,
            'status'      => $request->param('status/d'),
            'replynum '   => 0,
            'parent_id'   => $request->param('parent_id/d', 0),
            'create_time' => time(),
        ];
        
        // 添加数据
        $user = MongoComment::insert($data);
        if (!$user) {
            throw new \Exception('评论失败');
        }
        
        // 评论回复数加一
        if ($data['parent_id'] !== 0) {
            $parent_id = $request->param('parent_id/d');
            $this->buck->update(['id' => $parent_id], ['$inc' => ['replynum' => 1]]);
            $result = $this->manager($this->buck, $this->wirte);
            if (!$result) {
                throw new Fail('回复失败');
            }
            return success();
        }
    }
    
    public function updateComment($request)
    {
        // 组装数据
        $data = [
            'id'          => $request->param('id/d'),
            'content'     => $request->param('content/s'),
            'nickname'    => $request->param('nickname/s'),
        ];
        
        $comment = MongoComment::find($data['id']);
        if (!$comment) {
            throw new Miss('找不到该评论');
        }

        // $buck->update(['id'=>$data['id']], ['$set'=>$data], ['multi'=>true]);
        $this->buck->update(['id' => $data['id']], ['$set' => $data]);
        $result = $this->manager($this->buck, $this->wirte);
        if (!$result) {
            throw new \Exception('评论失败');
        }
    }

    public function deleteCommentById(int $id)
    {
        $comment = MongoComment::find($id);
        if (!$comment) {
            throw new Miss('找不到该评论');
        }

        $result =  $comment->delete();
        // $result = MongoComment::where('id', $id)->delete();
        if (!$result) {
            throw new Fail();
        }
    }

    // 文章点赞加一
    public function addLike(int $id)
    {
        $comment = MongoComment::find($id);
        if (!$comment) {
            throw new Miss('找不到该评论');
        }
        
        $this->buck->update(['id' => $id], ['$inc' => ['likenum' => 1]]);
        $result = $this->manager($this->buck, $this->wirte);
        if (!$result) {
            throw new Fail();
        }
    }
}
