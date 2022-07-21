<?php

namespace app\index\controller;

use app\common\lib\classes\RabbitMqWork;
use app\common\lib\classes\RabbitMq;
// use app\polymerize\tool\module\es\SearchBlog;
// use app\polymerize\tool\module\es\SyncBlog;
use think\Collection;

class Index extends Collection
{
    public function index()
    {
        // $this->send();
        // $this->addTask();
        // $this->sendQueue();
        // $this->sendDirect();
        $this->sendTopic();
        var_dump(11);
        die();
    }

    /* public function searchBlog()
    {
        // $id=1;
        // $res = SyncBlog::getInstance()->syncBlog($id);
        $search = '11';
        $res    = SearchBlog::getInstance()->searchBlog($search, 1, 100);
        var_dump($res);
        die();
        var_dump(1111);
        die();
    } */

    /**
     * 发送(普通)
     */
    public function send()
    {
        $msg = input('msg', 'This is work task!');
        $RabbitMqWork = new RabbitMqWork();
        $RabbitMqWork->send($msg);
    }

    /**
     * 发送(工作队列)
     */
    public function addTask()
    {
        $data         = input('data', 'This is work task!');
        $RabbitMqWork = new RabbitMqWork();
        $RabbitMqWork->addTask($data);
    }

    /**
     * 发送(扇形交换机)
     */
    public function sendQueue()
    {
        $data         = input('data', 'This is send queue1');
        $RabbitMqWork = new RabbitMqWork(RabbitMq::FANOUT);
        $RabbitMqWork->sendQueue($data);
    }

    /**
     * 发送(直接交换机)
     */
    public function sendDirect()
    {
        $data         = input('data', 'Hello World!');
        $routingKey   = input('routingKey', 'info');
        $RabbitMqWork = new RabbitMqWork(RabbitMq::DIRECT);
        $RabbitMqWork->sendDirect($routingKey, $data);
    }

    /**
     * 发送(主题交换机)
     */
    public function sendTopic()
    {
        $data         = input('data', 'Hello World!');
        $routingKey   = input('routingKey', 'lazy.boy');
        $RabbitMqWork = new RabbitMqWork(RabbitMq::TOPIC);
        $RabbitMqWork->sendTopic($routingKey, $data);
    }
}
