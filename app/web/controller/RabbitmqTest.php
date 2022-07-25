<?php

namespace app\web\controller;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use app\common\lib\classes\rabbitmq\RabbitMqWork;
use app\lib\exception\Fail;
use app\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqTest
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
        $msg          = $request->params['msg'];
        $RabbitMqWork = new RabbitMqWork();
        $RabbitMqWork->send($msg);
        return success("Send Message: " . $msg);
    }

    // 生产者 详细描述
    public function publisher_jinx(Request $request)
    {
        /* // 自己封装的连接，用于练习
        $connection = RabbitMqConnection::getConnection();
        $channel = $connection->channel();
        $msg = $request->params['msg'];
        RabbitMqConnection::send($channel, $msg);
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
        return success("Send Message: " . $msg); */

        // 创建连接rabbitmq的连接工厂对象，连接rabbitmq主机，获取连接对象
        $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'akali', '123456', '/akali');
        // $connection = new AMQPStreamConnection('192.168.0.184', 5672, 'akali', '123456', '/akali');
        // $connection = new AMQPStreamConnection('rabbitmq', 5672, 'akali', '123456', '/akali');

        // 获取连接中通道
        $channel = $connection->channel();

        // 这里要注意的是，生产者和消费者的队列参数必需一致
        // 通道绑定对应消息队列
        // 参数1:队列名称如果队列不存在时自动创建
        // 参数2:
        // 参数3:用来定义队列特性是否要持久化 true持久化队列 false不持久化
        // 参数4:exclusive是否独占队列 true 独占队列 false 不独占，如果是true，那么只能被当前通道绑定
        // 参数4:exclusive一般都是false，因为工作中，我们一般希望是共用一个通道
        // 参数5:autoDelete:是否在消费完成后自动删除队列 true自动删除 false不自动删除
        // 参数5:退出连接，没有消费者监听时才会自动删除队列
        $channel->queue_declare('hello', false, true, false, true);

        // 接收消息参数
        $msg = $request->params['msg'];

        // 生成消息
        // delivery_mode:2 声明消息持久，持久的队列 + 持久的消息在rabbitmq重启后不会消失
        // $msg = new AMQPMessage('Hello World!', array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        // 生成消息
        $amqpMsg = new AMQPMessage($msg);

        // 发布消息
        // 参数1:amqp消息对象
        // 参数2:交换机名称 不填写会使用默认的交换机amq.default
        // 参数3:队列名称
        // 参数4:传递消息额外设置
        $channel->basic_publish($amqpMsg, '', 'hello');

        // 关闭连接
        $channel->close();
        $connection->close();

        return success("Send Message: " . $msg);
    }

    // 添加工作队列
    public function work(Request $request)
    {
        $msg          = $request->params['msg'];
        $RabbitMqWork = new RabbitMqWork();
        for ($i = 0; $i < 20; $i++) {
            $RabbitMqWork->addTask($i . ' - ' . $msg);
        }

        return success("Send Message: " . $msg);
    }

    // 添加工作队列
    public function work_jinx(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();
        // 获取数据
        $msg = $request->params['msg'];
        // 发送
        $channel->queue_declare('task', false, true, false, true);
        for($i = 0; $i < 20; $i++){
            $amqpMsg = new AMQPMessage($i.' - '.$msg);
            // 生产消息
            $channel->basic_publish($amqpMsg, '', 'task');
        }
        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
        // 返回结果
        return success("Send Message: " . $msg);
    }

    // 广播
    public function fanout(Request $request)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();
        // 获取数据
        $msg = $request->params['msg'];
        $amqpMsg = new AMQPMessage($msg);
        // 将通道声明指定交换机
        // 参数1：交换机名称 随便起名 交换机不存在的时候会自动创建
        // 参数2：交换机类型 fanout 广播类型，这里管理界面的exchanges能看到amq.fanout自带的交换机
        $channel->exchange_declare('fanout_exchange', 'fanout');
        // $channel->exchange_declare('fanout_exchange', 'fanout',false,true,true);
        // 发送消息
        // 广播模式下，routing_key 没有任何意义，不需要赋值
        $channel->basic_publish($amqpMsg, 'fanout');
        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
        // 返回结果
        return success("Send Message: " . $msg);
    }

}
