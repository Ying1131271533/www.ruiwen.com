<?php
namespace app\web\controller;

use app\common\mongo\Info;
use app\common\mongo\User;
use app\lib\exception\Fail;
use app\lib\exception\Miss;
use app\lib\exception\Params;
use app\Request;
use app\web\logic\Mongo as LogicMongo;

class Mongo
{
    public function index(Request $request)
    {
        // $user = User::with('info')->order('_id', 'desc')->select();
        $user = User::getPageData($request->page, $request->size);

        return success($user);
    }

    public function read(Request $request)
    {
        // $use = User::command();
        // $id   = $request->params['id'];
        $id   = (int) $request->params['id'];
        $user = LogicMongo::findUserById($id);
        return success($user);
    }

    public function save(Request $request)
    {
        // 组装数据
        $data = [
            'id'     => $request->param('id/d'),
            'name'   => $request->param('name/s'),
            'age'    => $request->param('age/d'),
            'gender' => $request->param('gender/s'),
        ];

        $infoData = [
            'profession' => $request->param('profession/s'),
            'user_id'    => $data['id'],
        ];

        $result = User::where('id', $data['id'])->find();
        if ($result) {
            throw new Fail('用户id已存在');
        }

        $user = User::create($data);
        if (!$user) {
            throw new \Exception('用户创建失败');
        }

        $info = Info::create($infoData);
        // $info = $user->info()->save($infoData);
        if (!$info) {
            throw new \Exception('用户信息创建失败');
        }

        return success();
    }

    // 还是用不了事务
    public function save_jinx(Request $request)
    {
        // 组装数据
        $data = [
            'id'     => $request->param('id/d'),
            'name'   => $request->param('name/s'),
            'age'    => $request->param('age/d'),
            'gender' => $request->param('gender/s'),
        ];

        $infoData = [
            'profession' => $request->param('profession/s'),
            'user_id'    => $data['id'],
        ];

        $result = User::where('id', $data['id'])->find();
        if ($result) {
            throw new Fail('用户id已存在');
        }

        User::startTrans();
        try {
            $user = User::create($data);
            if (!$user) {
                throw new \Exception('用户创建失败');
            }
            
            $info = $user->info()->save($infoData);
            if ($info) {
                throw new \Exception('用户信息创建失败');
            }

            User::commit();
            return success();
        } catch (\Exception $e) {
            User::rollback();
            throw new Fail($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        // 组装数据
        $data = [
            'id'     => $request->param('id/d'),
            'name'   => $request->param('name/s'),
            // 'age'    => $request->param('age/d'),
            'gender' => $request->param('gender/s'),
        ];
        
        $user = User::where('id', $data['id'])->find();
        if (!$user) {
            throw new Miss();
        }

        // $params = $request->params;
        // halt($data);
        $result = User::where('id', $data['id'])->update($data);
        if (!$result) {
            throw new Fail();
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
}
