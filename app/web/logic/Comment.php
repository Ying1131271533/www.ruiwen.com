<?php
namespace app\web\logic;

use app\common\mongo\Comment as MongoComment;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteConcern;

class Comment
{
    protected $manager;
    protected $buck;
    protected $wirte;

    public function __construct()
    {
        // 单机实例
        $this->manager = new Manager("mongodb://myroot:123456@192.168.0.184:27017");
        // 副本集，关闭了27017，却显示不是主节点(not primary)，所以暂时不会用，不过最起码还有可以读
        // $this->manager = new Manager("mongodb://akali:123456@192.168.0.184:27017,192.168.0.184:27018,192.168.0.184:27019/www_ruiwen_com?connect=replicaSet&slaveOk=true&replicaSet=myrs");
        // $this->manager = new Manager("mongodb://192.168.0.184:27017,192.168.0.184:27018,192.168.0.184:27019");
        // 路由节点连接有效，就不知道关闭了27017会不会跟上面一样
        // $this->manager = new Manager("mongodb://192.168.0.184:27017,192.168.0.184:27117");
        $this->buck  = new BulkWrite(['ordered' => true]);
        $this->wirte = new WriteConcern(WriteConcern::MAJORITY, 1000);
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
    public function getCommentListByParentId($paren_id, int $page, int $size)
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

    // cmd命令找评论
    public function findCommentById($id)
    {
        $query  = new Query(['id' => $id]);
        $result = $this->manager->executeQuery('www_ruiwen_com.comment', $query);
        $data   = [];
        foreach ($result as $value) {
            $data[] = $value;
        }

        if (empty($data)) {
            throw new Miss();
        }

        return $data;
    }

    public function saveComment($request)
    {
        // 创建objectid对象
        $oid = new \MongoDB\BSON\ObjectId();
        $id  = sprintf("%s", $oid);

        // 组装数据
        $data = [
            '_id'         => $oid,
            'id'          => $id,
            'user_id'     => $request->param('user_id/s'),
            'article_id'  => $request->param('article_id/s'),
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
            'id'       => $request->param('id/s'),
            'content'  => $request->param('content/s'),
            'nickname' => $request->param('nickname/s'),
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

    public function deleteCommentById($id)
    {
        $comment = MongoComment::find($id);
        if (!$comment) {
            throw new Miss('找不到该评论');
        }

        $result = $comment->delete();
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

        return success();
    }
}
