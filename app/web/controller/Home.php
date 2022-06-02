<?php

namespace app\web\controller;

use app\common\model\Akali;
use app\common\mongo\User;
use think\db\builder\Mongo;

class Home
{
    public function index()
    {
        $data = [
            'name' => '光辉',
            'age' => 17,
            'gender' => '女',
        ];
        // $result = User::insert($data);
        // $result = User::where('_id', '629887ed04cfb81ffd0f4739')->update($data);
        // $result = User::where('name', '光辉')->delete();
        // dump($result);

        // $user = User::skip(3)->limit(2)->order('_id', 'desc')->select()->toArray();
        // $user = User::paginate(2);

        $user = User::select()->toArray();
        halt($user);
        

        return success();
    }

    public function akali()
    {
        return success('阿卡丽');
    }

    public function ying()
    {
        return success('樱之节');
    }

    public function jinx()
    {
        return success('金克丝');
    }

    // 脚本尝试：curl 124.71.218.160/web/home/shell
    public function shell()
    {
        $result = Akali::create(['name' => '神织知更']);
        // return success($result);
        // if(!$result) $this->shell();
    }
}
