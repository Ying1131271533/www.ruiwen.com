<?php

namespace app\web\controller;

use app\lib\exception\Fail;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use app\Request;

class RabbitTest
{
    /* protected $config = array(
    'host'     => 'rabbitmq',
    'vhost'    => '/akali',
    'port'     => 5672,
    'login'    => 'akali',
    'password' => '123456',
    ); */
    protected $config = array(
        'host'     => '192.168.0.184',
        'vhost'    => '/akali',
        'port'     => 5672,
        'login'    => 'akali',
        'password' => '123456',
    );

    public function __construct()
    {}

    public function index()
    {
        $conn = new \AMQPConnection($this->config);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        halt($conn->connect());
        return success('神织恋');
    }

    // 生产者
    public function publisher(Request $request)
    {
        // 创建连接rabbitmq的连接工厂对象，连接rabbitmq主机，获取连接对象
        $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'akali', '123456', '/akali');
        // $connection = new AMQPStreamConnection('192.168.0.184', 5672, 'akali', '123456', '/akali');
        // $connection = new AMQPStreamConnection('rabbitmq', 5672, 'akali', '123456', '/akali');

        // 获取连接中通道
        $channel = $connection->channel();

        // 通道绑定对应消息队列
        // 参数1:队列名称如果队列不存在时自动创建
        // 参数2:用来定义队列特性是否要持久化 true持久化队列 false不持久化
        // 参数3:exclusive是否独占队列 true 独占队列 false 不独占
        // 参数4:autoDelete:是否在消费完成后自动删除队列 true自动删除 false 不自动删除
        // 参数5:额外附加参数
        $channel->queue_declare('akali_queue', false, true, false, false);

        // 生成消息
        // delivery_mode:2 声明消息持久，持久的队列 + 持久的消息在rabbitmq重启后不会消失
        // $msg = new AMQPMessage('Hello World!', array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        
        // 接收消息参数
        if(!isset($request->params['msg']) || empty($request->params['msg'])){
            $msg = 'Hello World!';
        }else{
            $msg = $request->params['msg'];
        }

        $msgAmqp = new AMQPMessage($msg);

        // 发布消息
        // 参数1:amqp消息对象
        // 参数2:交换机名称
        // 参数3:队列名称
        // 参数4:传递消息额外设置
        $channel->basic_publish($msgAmqp, '', 'akali_queue');

        // 关闭连接
        $channel->close();
        $connection->close();

        return success("Send Message: " . $msg);
    }
}
