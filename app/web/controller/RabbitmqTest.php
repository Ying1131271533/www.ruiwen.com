<?php

namespace app\web\controller;

use app\common\lib\classes\rabbitmq\RabbitMq;
use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use app\common\lib\classes\rabbitmq\RabbitMqWork;
use app\lib\exception\Fail;
use app\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use think\cache\driver\Redis;

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
        // 参数4:exclusive是否独占队列
        // true 独占队列 false 不独占，如果是true，那么只能被当前通道绑定
        // exclusive一般都是false，因为工作中，我们一般希望是共用一个通道
        // 参数5:autoDelete:是否在消费完成后自动删除队列 true自动删除 false不自动删除
        // 退出连接，没有消费者监听时是否自动删除队列
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
    public function publisher_confirm(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();

        // 获取连接中通道
        $channel = $connection->channel();

        // 已发布的消息数组，不知道要不要用redis保存，用了redis耗时超大
        // $outStandingConfirm = [];

        // 确认投放队列，并将队列持久化
        $channel->queue_declare('hello', false, true, false, false);
        // 这里只是看看耗时，不持久化
        // $channel->queue_declare('hello', false, false, false, false);

        // 异步回调消息确认 成功
        $channel->set_ack_handler(
            function (AMQPMessage $message) {
                echo "Message acked with content " . $message->body . PHP_EOL;
                echo "Tag " . $message->delivery_info['delivery_tag'] . PHP_EOL;
                // 删除已经确认的消息
                // (new Redis(config('app.redis')))->hDel('hello', $message->delivery_info['delivery_tag']);
            }
        );
        // 异步回调消息确认 失败
        $channel->set_nack_handler(
            function (AMQPMessage $message) {
                echo "Message nacked with content " . $message->body . PHP_EOL;
            }
        );

        // 开启消息发布确认，选择为 confirm 模式（此模式不可以和事务模式 兼容）
        $channel->confirm_select();

        // 获取当前毫秒时间
        $time = msectime();

        for ($i = 0; $i < 100; $i++) {
            // 接收消息参数
            // $msg     = $request->params['msg'];
            $msg     = '消息 ' . $i;
            $amqpMsg = new AMQPMessage($msg);
            // 发布
            $channel->basic_publish($amqpMsg, '', 'hello');
            // 记录下所有要发送的消息 消息的总和
            // (new Redis(config('app.redis')))->hset('hello', $amqpMsg->delivery_info['delivery_tag'], $i);

            // 阻塞等待消息确认，单个确认 341ms
            // $channel->wait_for_pending_acks();

            // 批量确认，每次确认100个 187ms
            /*  if ($i % 100 == 0) {
        $channel->wait_for_pending_acks();
        } */
        }

        // 异步消息确认 140ms
        $channel->wait_for_pending_acks();

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        // return success("Send Message: " . $msg);
        return success('耗时' . (msectime() - $time) . 'ms');
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

    // 死信队列以及延迟
    public function publisher_dead(Request $request)
    {
        // 获取连接对象，order虚拟机
        $connection = RabbitMqConnection::getConnection(['vhost' => 'order']);
        // 获取通道
        $channel = $connection->channel();

        // 普通交换机名称
        $normal_exchange = 'normal_exchange';
        // 普通队列名称
        $normal_queue = 'normal_queue';
        // 普通routing_key
        $normal_routing_key = 'normal_routing_key';
        // 设置延迟时间10s过期，等待10秒未进行消费，数据会自动跑去死信队列中(还真跑到死信队列了)
        $ttl = 10000;

        // 死信队列名称
        $dead_queue = 'dead_queue';
        // 死信交换机名称
        $dead_exchange = 'dead_exchange';
        // 死信routing_key
        $dead_routing_key = 'dead_routing_key';

        /*********************  普通队列  *********************/

        // 声明普通交换机和死信交换机 类型为direct
        $channel->exchange_declare($normal_exchange, 'direct', false, false, false);

        // 设置队列中数据存活时间、死信队列、死信路由key
        $arguments = new AMQPTable([
            // 第一种死信情况 消息TTL过期
            // 如果不设置放到其它队列arguments，不知道是不起效，还是回到了原本队列中
            // 'x-message-ttl'             => $ttl,
            'x-dead-letter-exchange'    => $dead_exchange,
            'x-dead-letter-routing-key' => $dead_routing_key,
            // 第二种死信情况 队列达到最大长度
            // 设置队列长度的限制
            // 'x-max-length'              => 6,
            // 设置队列最大字节数
            // 'x-max-length-bytes' => 1024;
        ]);
        /* $args = new AMQPTable();
        $args->set('x-message-ttl', $ttl);
        $args->set('x-dead-letter-exchange', $dead_exchange);
        $args->set('x-dead-letter-routing-key', $dead_routing_key); */

        // 声明普通队列
        $channel->queue_declare($normal_queue, false, true, false, false, false, $arguments);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($normal_queue, $normal_exchange, $normal_routing_key);

        /*********************  死信队列  *********************/

        // 声明死信交换机和队列
        $channel->exchange_declare($dead_exchange, 'direct', false, false, false);
        $channel->queue_declare($dead_queue, false, true, false, false);
        $channel->queue_bind($dead_queue, $dead_exchange, $dead_routing_key);

        /*********************  发送消息  *********************/

        // 接受数据
        $msg = $request->params['msg'];
        /* // amqp对象，消息持久化
        $amqpMsg = new AMQPMessage($msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);

        // 发送消息
        $channel->basic_publish($amqpMsg, $normal_exchange, $normal_routing_key); */

        // 模拟第二种死信情况 长度限制
        for ($i = 1; $i < 11; $i++) {
            $amqpMsg = new AMQPMessage($i, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
            $channel->basic_publish($amqpMsg, $normal_exchange, $normal_routing_key);
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);

        // 返回
        return success('Send Message: ' . $msg);
    }

}
