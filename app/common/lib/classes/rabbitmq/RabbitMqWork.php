<?php
//供外部调用的rabbitMq类
 
namespace app\common\lib\classes\rabbitmq;
 
use app\common\lib\classes\rabbitmq\RabbitMq;
 
class RabbitMqWork
{
    private $RabbitMq;
 
    public function __construct($exchageType = '')
    {
        $this->RabbitMq = RabbitMq::instance($exchageType);
    }
 
    /**
     * 发送(普通)
     */
    public function send($msg)
    {
        $this->RabbitMq->send($msg);
    }
 
    /**
     * 接收(普通)
     * @param $callback
     */
    public function receive($callback)
    {
        $this->RabbitMq->receive($callback);
    }
 
    /**
     * 发送(工作队列)
     * @param $data
     */
    public function addTask($data)
    {
        $this->RabbitMq->addTask($data);
    }
 
    /**
     * 接收(工作队列)
     * @param $callback
     */
    public function workTask($callback)
    {
        $this->RabbitMq->workTask($callback);
    }
 
    /**
     * 发布(扇形交换机) 广播
     * @param $data
     */
    public function sendQueue($data)
    {
        $this->RabbitMq->sendQueue($data);
    }
 
    /**
     * 订阅(扇形交换机)
     * @param $callback
     */
    public function subscribeQueue($callback)
    {
        $this->RabbitMq->subscribeQueue($callback);
    }
 
    /**
     * 发送(直接交换机) 订阅模型
     * @param $bindingKey
     * @param $data
     */
    public function sendDirect($routingKey, $data)
    {
        $this->RabbitMq->sendDirect($routingKey, $data);
    }
 
    /**
     * 接收(直接交换机) 
     * @param \Closure $callback
     * @param array $bindingKeys
     */
    public function receiveDirect(\Closure $callback, array $bindingKeys)
    {
        $this->RabbitMq->receiveDirect($callback, $bindingKeys);
    }
 
    /**
     * 发送(主题交换机)
     * @param $routingKey
     * @param $data
     */
    public function sendTopic($routingKey, $data)
    {
        $this->RabbitMq->sendTopic($routingKey, $data);
    }
 
    /**
     * 接收(主题交换机)
     * @param \Closure $callback
     * @param array $bindingKeys
     */
    public function receiveTopic(\Closure $callback, array $bindingKeys)
    {
        $this->RabbitMq->receiveTopic($callback, $bindingKeys);
    }
}