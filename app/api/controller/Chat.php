<?php

namespace app\api\controller;

use app\BaseController;
use think\App;
use WebSocket\Client;
use app\common\logic\api\Chat as ChatLogic;

class Chat extends BaseController
{
    protected $logic = '';

    public function __construct(App $app)
    {
        // 控制器初始化
        parent::__construct($app);
        $this->logic = new ChatLogic();
    }

    public function test()
    {
        $client = new Client('ws://124.71.218.160:9502?token=' . $this->getToken());
        // $client = new Client('wss://124.71.218.160:9502?token=' . $this->getToken());
        $client->send('哎嘿');
        // 接收服务端返回的信息
        dump($client->receive());
        $client->close();
    }

    // 聊天记录
    public function record()
    {
        // 接收参数
        $params = $this->request->params;
        $params['uid'] = $this->getUid();
        $record  = $this->logic->record($params);
        // 返回结果
        return $this->success($record);
    }
}
