<?php

namespace app\web\controller;

use app\lib\exception\Fail;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

    public function testSendMessage()
    {
        return success('阿卡丽');
    }

    // 生产者
    public function publisher()
    {
        $connection = new AMQPStreamConnection('192.168.0.184', 5672, 'akali', '123456', '/akali');
        $channel    = $connection->channel();

        $channel->queue_declare('akali', false, true, false, false);

        $argv = [];
        $data = implode(' ', array_slice($argv, 1));
        if (empty($data)) {
            $data = "Hello World!";
        }

        $msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        $channel->basic_publish($msg, '', 'akali');

        echo " Send Message: ", $data, "\n";

        $channel->close();
        $connection->close();
    }

    // 消费者
    public function consumer()
    {
        // 声明一个路由键
        $routingKey = 'key_1';
        // 设置一个交换机名称
        $exchangeName = 'exchange_1';

        // 声明交换机
        try {
            // 加载连接类 connection.php
            include '../extend/lib/RabbitMQConnection.php';

            //创建一个消息队列
            $q = new \AMQPQueue($ch);

            //设置队列名称
            $q->setName('queue_1');

            //设置队列持久化
            $q->setFlags(AMQP_DURABLE);

            //声明消息队列
            $q->declareQueue();

            // 交换机和队列通过$routingKey进行绑定
            $q->bind($ex->getName(), $routingKey);

            include '../extend/lib/Akali.php';

            //设置消息队列消费者回调方法
            $q->consume('recevie');

        } catch (\AMQPConnectionException $e) {
            echo '创建连接异常：' . $e->getMessage();
            exit();
        } catch (\AMQPChannelException $e) {
            echo '创建通道异常：' . $e->getMessage();
            exit();
        } catch (\AMQPQueueException $e) {
            echo '创建消息队列异常：' . $e->getMessage();
            exit();
        } catch (\AMQPEnvelopeException $e) {
            echo '消息消费异常：' . $e->getMessage();
            exit();
        }
    }

    // 生产者
    public function publisher_jinx()
    {
        //创建一个新的连接, 连接到broker
        $cnn = new \AMQPConnection($this->config);
        if (!$cnn->connect()) {
            echo "Cannot connect to the broker";
            exit();
        }
        $ch = new \AMQPChannel($cnn);
        $ex = new \AMQPExchange($ch);
        //消息的路由键，一定要和消费者端一致
        $routingKey = 'key_1';
        //交换机名称，一定要和消费者端一致，
        $exchangeName = 'exchange_1';
        $ex->setName($exchangeName);
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        $ex->setFlags(AMQP_DURABLE);
        $ex->declareExchange();
        //创建20个消息
        for ($i = 1; $i <= 20; $i++) {
            //消息内容
            $msg = array(
                'data'  => 'message_' . $i,
                'hello' => 'world',
            );
            //发送消息到交换机，并返回发送结果
            //delivery_mode:2声明消息持久，持久的队列+持久的消息在RabbitMQ重启后才不会丢失
            echo "Send Message:" . $ex->publish(json_encode($msg), $routingKey, AMQP_NOPARAM, array('delivery_mode' => 2)) . "\n";
            //代码执行完毕后进程会自动退出
        }
    }

    // 消费者
    public function consumer_jinx()
    {
        //连接broker
        $cnn = new \AMQPConnection($config);
        if (!$cnn->connect()) {
            echo "Cannot connect to the broker";
            exit();
        }

        //在连接内创建一个通道
        $ch = new \AMQPChannel($cnn);
        //创建一个交换机
        $ex = new \AMQPExchange($ch);
        //声明路由键
        $routingKey = 'key_1';
        //声明交换机名称
        $exchangeName = 'exchange_1';
        //设置交换机名称
        $ex->setName($exchangeName);
        //设置交换机类型
        //AMQP_EX_TYPE_DIRECT:直连交换机
        //AMQP_EX_TYPE_FANOUT:扇形交换机
        //AMQP_EX_TYPE_HEADERS:头交换机
        //AMQP_EX_TYPE_TOPIC:主题交换机
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        //设置交换机持久
        $ex->setFlags(AMQP_DURABLE);
        //声明交换机
        $ex->declareExchange();
        //创建一个消息队列
        $q = new \AMQPQueue($ch);
        //设置队列名称
        $q->setName('queue_1');
        //设置队列持久
        $q->setFlags(AMQP_DURABLE);
        //声明消息队列
        $q->declareQueue();
        //交换机和队列通过$routingKey进行绑定
        $q->bind($ex->getName(), $routingKey);
        //接收消息并进行处理的回调方法
        function receive($envelope, $queue)
        {
            //休眠两秒，
            sleep(2);
            echo $envelope->getBody() . "\n";
            //显式确认，队列收到消费者显式确认后，会删除该消息
            $queue->ack($envelope->getDeliveryTag());
        }

        //设置消息队列消费者回调方法，并进行阻塞
        $q->consume("receive");
    }

    // 生产者
    public function publisher_akali()
    {
        $e_name = 'e_linvo'; // 交换机名
        // $q_name = 'q_linvo'; // 无需队列名
        $k_route = 'key_1'; // 路由key

        // 创建连接
        $conn = new \AMQPConnection($this->config);
        if (!$conn->connect()) {
            throw new Fail("Cannot connect to the broker!\n");
        }

        // 创建通道
        $channel = new \AMQPChannel($conn);

        // 创建交换机对象
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        date_default_timezone_set("Asia/Shanghai");
        // 发送消息
        // $channel->startTransaction(); // 开始事务
        for ($i = 0; $i < 5; ++$i) {
            sleep(1); // 休眠1秒
            // 消息内容
            $message = "TEST MESSAGE!" . date("h:i:sa");
            echo "Send Message:" . $ex->publish($message, $k_route) . "\n";
        }
        // $channel->commitTransaction(); // 提交事务

        $conn->disconnect();
    }

    // 消费者
    public function consumer_akali()
    {
        $e_name  = 'e_linvo'; // 交换机名
        $q_name  = 'q_linvo'; // 队列名
        $k_route = 'key_1'; // 路由key

        // 创建连接
        $conn = new \AMQPConnection($this->config);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }

        // 创建通道
        $channel = new \AMQPChannel($conn);

        // 创建交换机
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        $ex->setType(AMQP_EX_TYPE_DIRECT); // direct类型
        $ex->setFlags(AMQP_DURABLE); // 持久化
        echo "Exchange Status:" . $ex->declare() . "\n";

        // 创建队列
        $q = new \AMQPQueue($channel);
        $q->setName($q_name);
        $q->setFlags(AMQP_DURABLE); // 持久化
        echo "Message Total:" . $q->declare() . "\n";

        // 绑定交换机与队列，并指定路由键
        echo 'Queue Bind: ' . $q->bind($e_name, $k_route) . "\n";

        // 阻塞模式接收消息
        echo "Message:\n";
        while (true) {
            $q->consume('processMessage');
            // $q->consume('processMessage', AMQP_AUTOACK); // 自动ACK应答
        }
        $conn->disconnect();
    }

    /**
     * 消费回调函数 处理消息
     */
    public function processMessage($envelope, $queue)
    {
        $msg = $envelope->getBody();
        echo $msg . "\n"; //处理消息
        $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
    }
}
