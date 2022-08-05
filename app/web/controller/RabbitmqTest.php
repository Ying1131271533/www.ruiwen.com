<?php

namespace app\web\controller;

use app\common\lib\classes\rabbitmq\RabbitMq;
use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use app\common\lib\classes\rabbitmq\RabbitMqWork;
use app\lib\exception\Fail;
use app\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqTest
{
    protected $config = array(
        'host'     => '192.168.0.184',
        // 'host'     => 'rabbitmq',
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
    public function publisher_akali(Request $request)
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

        // 获取连接
        $connection = RabbitMqConnection::getConnection();
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
        // 参数5:退出连接，没有消费者监听时是否自动删除队列
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
        // 参数3:队列名称，这不是路由吗？
        // 参数4:传递消息额外设置
        $channel->basic_publish($amqpMsg, '', 'hello');

        // 关闭连接
        $channel->close();
        $connection->close();

        return success("Send Message: " . $msg);
    }

    // 生产者 发布确认
    public function publisher(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();

        // 获取连接中通道
        $channel = $connection->channel();

        // 确认投放队列，并将队列持久化
        $channel->queue_declare('hello', false, true, false, false);

        //异步回调消息确认
        $channel->set_ack_handler(
            function (AMQPMessage $message) {
                echo "Message acked with content " . $message->body . PHP_EOL;
            }
        );
        $channel->set_nack_handler(
            function (AMQPMessage $message) {
                echo "Message nacked with content " . $message->body . PHP_EOL;
            }
        );
        //开启消息确认
        $channel->confirm_select();

        // 接收消息参数
        $msg     = $request->params['msg'];
        $amqpMsg = new AMQPMessage($msg,array('delivery_mode' =>  AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($amqpMsg, '', 'hello');

        //阻塞等待消息确认
        $channel->wait_for_pending_acks();

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

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
        // 开启发布确认
        $channel->confirm_select();
        // 获取数据
        $msg = $request->params['msg'];
        // 发送
        $channel->queue_declare('task', false, true, false, true);
        for ($i = 0; $i < 20; $i++) {
            $amqpMsg = new AMQPMessage($i . ' - ' . $msg);
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
        $RabbitMqWork = new RabbitMqWork(RabbitMq::FANOUT);
        // 获取数据
        $msg = $request->params['msg'];
        $RabbitMqWork->sendQueue($msg);
        return success("Send Message: " . $msg);
    }

    // 广播
    public function fanout_jinx(Request $request)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();
        // 获取数据
        $msg     = $request->params['msg'];
        $amqpMsg = new AMQPMessage($msg);
        // 将通道声明指定交换机
        // 参数1：交换机名称 随便起名 交换机不存在的时候会自动创建
        // 参数2：交换机类型 fanout 广播类型，这里管理界面的exchanges能看到amq.fanout自带的交换机
        // 参数3：是否检测同名队列
        // 参数4：是否开启队列持久化
        // 参数5：通道关闭后是否删除队列 不自动删除队列
        $channel->exchange_declare('logs', 'fanout', false, true, false);
        // 发送消息
        // fanout广播模式下，routing_key 没有任何意义，不需要赋值
        $channel->basic_publish($amqpMsg, 'logs');
        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);
        // 返回结果
        return success("Send Message: " . $msg);
    }

    // 订阅模型 路由
    public function direct(Request $request)
    {
        // 接受数据
        $routing_key  = $request->params['routing_key'];
        $msg          = $request->params['msg'];
        $RabbitMqWork = new RabbitMqWork(RabbitMq::DIRECT);
        $RabbitMqWork->sendDirect($routing_key, $msg);
        return success("Send Message: " . $msg);
    }

    // 订阅模型-direct 固定频道
    public function direct_jinx(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取连接通道
        $channel = $connection->channel();
        // 通过通道声明交换机
        // 参数1：交换机名称
        // 参数2：direct 路由模式
        $channel->exchange_declare('logs_direct', 'direct', false, true, true);
        // 接收数据
        $routing_key = $request->params['routing_key'];
        $msg         = $request->params['msg'];
        // 获取消息对象
        $amqpMsg = new AMQPMessage($msg);
        // 发送消息
        $channel->basic_publish($amqpMsg, 'logs_direct', $routing_key);
        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection); // 返回结果
        return success("Send Message: " . $msg);
    }

    // 订阅模型-Topic 动态路由
    public function topic(Request $request)
    {
        $RabbitMqWork = new RabbitMqWork(RabbitMq::TOPIC);
        // 接收参数
        $route_key = $request->params['route_key'];
        $msg       = $request->params['msg'];
        // 发送消息
        $RabbitMqWork->sendTopic($route_key, $msg);
        // 返回结果
        return success('Send Message: ' . $msg);
    }

    // 订阅模型-Topic 动态路由
    public function topic_jinx(Request $request)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        $channel    = $connection->channel();

        // 声明交换机以及交换机类型 topic
        $channel->exchange_declare('topics', 'topic', false, true, false);

        // 获取参数
        $route_key = $request->params['route_key'];
        $msg       = $request->params['msg'];
        $amqpMsg   = new AMQPMessage($msg);

        // 发布消息
        $channel->basic_publish($amqpMsg, 'topics', $route_key);

        // 关闭资源
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);
        return success("Send Message: " . $msg);
    }
}
