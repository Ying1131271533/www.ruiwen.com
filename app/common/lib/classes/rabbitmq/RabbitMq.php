<?php
//rabbitMq实现的基础类
 
namespace app\common\lib\classes\rabbitmq;
 
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
 
class RabbitMq
{
    static private $instance;
    static private $connection;
    static private $channel;
    const DIRECT = 'direct';
    const TOPIC = 'topic';
    const HEADERS = 'headers';
    const FANOUT = 'fanout';
    static private $exchangeNames = [
        self::DIRECT => 'direct_exchange',
        self::TOPIC => 'topic_exchange',
        self::HEADERS => 'headers_exchange',
        self::FANOUT => 'fanout_exchange',
    ];
    // 路由键类型，自己定义
    const SEVERITYS = [
        'info',
        'warning',
        'error'
    ];
    static private $exchangeName = '';
 
    /**
     * RabbitMq constructor.
     * @param $exchangeType
     */
    private function __construct($exchangeType)
    {
        $host = config('app.rabbitmq.host');
        $port = config('app.rabbitmq.port');
        $login = config('app.rabbitmq.login');
        $password = config('app.rabbitmq.password');
        $vhost = config('app.rabbitmq.vhost');
        self::$connection = new AMQPStreamConnection($host, $port, $login, $password, $vhost);
        // self::$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        self::$channel = self::$connection->channel();
        if (!empty($exchangeType)) {
            self::$exchangeName = self::$exchangeNames[$exchangeType];
            self::$channel->exchange_declare(
                self::$exchangeName, //交换机名称
                $exchangeType, //路由类型
                false, //don't check if a queue with the same name exists 是否检测同名队列
                true, //the queue will not survive server restarts 是否开启队列持久化
                false //the queue will be deleted once the channel is closed. 通道关闭后是否删除队列
            );
        }
    }
 
    /**
     * 实例化
     * @param string $exchangeType
     * @return RabbitMq
     */
    public static function instance($exchangeType = '')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($exchangeType);
        }
        return self::$instance;
    }
 
    /**
     * 防止被外部复制
     */
    private function __clone()
    {
    }
 
    /**
     * 简单的发送
     */
    public function send($msg)
    {
        self::$channel->queue_declare('hello', false, true, false, true);
        if (empty($msg)) $msg = 'Hello World!';
        $amqpMsg = new AMQPMessage(
            $msg,
            // 这里是消息持久化
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        self::$channel->basic_publish($amqpMsg, '', 'hello');
        // echo "[X] Sent 'Hello World!'\n";
    }
 
    /**
     * 简单的接收
     * @param $queueName
     * @param $callback
     */
    public function receive($callback)
    {
        self::$channel->queue_declare('hello', false, true, false, true);
        echo "[*] Waiting for messages. To exit press CTRL+C\n";
 
        self::$channel->basic_consume('hello', '', false, false, false, false, $callback);
 
        while (count(self::$channel->callbacks)) {
            self::$channel->wait();
        }
    }
 
    /**
     * 添加工作队列
     * @param string $data
     */
    public function addTask($data = '')
    {
        self::$channel->queue_declare('task', false, true, false, true);
        if (empty($data)) $data = 'Hello World!';
        $msg = new AMQPMessage(
            $data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        self::$channel->basic_publish($msg, '', 'task');
 
        // echo "[x] Sent $data \n";
    }
 
    /**
     * 执行工作队列
     * @param $callback
     */
    public function workTask($callback)
    {
        self::$channel->queue_declare('task', false, true, false, true);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        
        // 能者多劳模式
        // 参数2：每一次从队列获取多少个消息，当为1时，只能获取一条，处理完获取下一条
        // 当为0时不限制，所以队列中的消息可以轮询着一次性发完
        self::$channel->basic_qos(null, 1, null);
        // 关闭自动确认 no_ack = false
        self::$channel->basic_consume('task', '', false, false, false, false, $callback);
 
        while (count(self::$channel->callbacks)) {
            self::$channel->wait();
        }
    }
 
    /**
     * 发布(广播)
     * @param string $data
     */
    public function sendQueue($data = '')
    {
        if (empty($data)) $data = 'Hello World!';
        $msg = new AMQPMessage($data);
        self::$channel->basic_publish($msg, self::$exchangeName);
        // echo "[x] Sent $data \n";
    }
 
    /**
     * 订阅
     * @param $callback
     */
    public function subscribeQueue($callback)
    {
        // 获取临时队列名称
        list($queue_name, ,) = self::$channel->queue_declare(
            "", //队列名称
            false, //don't check if a queue with the same name exists 是否检测同名队列
            true, //the queue will not survive server restarts 是否开启队列持久化
            true, //the queue might be accessed by other channels 队列是否可以被其他队列访问
            false //the queue will be deleted once the channel is closed. 通道关闭后是否删除队列
        );
        // 绑定队列和交换机
        self::$channel->queue_bind($queue_name, self::$exchangeName);
        echo "[*] Waiting for logs. To exit press CTRL+C \n";
        self::$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
 
        while (count(self::$channel->callbacks)) {
            self::$channel->wait();
        }
    }
 
    /**
     * 发送(直接交换机)
     * @param $routingKey
     * @param string $data
     */
    public function sendDirect($routingKey, $data = '')
    {
        if (empty($data)) $data = "Hello World!";
        $msg = new AMQPMessage($data);
        self::$channel->basic_publish($msg, self::$exchangeName, $routingKey);
        echo "[x] Sent $routingKey:$data \n";
    }
 
    /**
     * 接收(直接交换机)
     * @param \Closure $callback
     * @param array $bindingKeys
     */
    public function receiveDirect(\Closure $callback, array $bindingKeys)
    {
        list($queue_namme, ,) = self::$channel->queue_declare('', false, true, true, false);
        foreach ($bindingKeys as $bindingKey) {
            self::$channel->queue_bind($queue_namme, self::$exchangeName, $bindingKey);
        }
        echo "[x] Waiting for logs. To exit press CTRL+C \n";
        self::$channel->basic_consume($queue_namme, '', false, true, false, false, $callback);
        while (count(self::$channel->callbacks)) {
            self::$channel->wait();
        }
    }
 
    /**
     * 发送(主题交换机)
     * @param $routingKey
     * @param string $data
     */
    public function sendTopic($routingKey, $data = '')
    {
        if (empty($data)) $data = "Hello World!";
        $msg = new AMQPMessage($data);
        self::$channel->basic_publish($msg, self::$exchangeName, $routingKey);
        // echo " [x] Sent ", $routingKey, ':', $data, " \n";
    }
 
    /**
     * 接收(主题交换机)
     * @param \Closure $callback
     * @param array $bindingKeys
     */
    public function receiveTopic(\Closure $callback, array $bindingKeys)
    {
        list($queueName, ,) = self::$channel->queue_declare("", false, true, true, false);
        foreach ($bindingKeys as $bindingKey) {
            self::$channel->queue_bind($queueName, self::$exchangeName, $bindingKey);
        }
 
        echo '[*] Waiting for logs. To exit press CTRL+C', "\n";
        self::$channel->basic_consume($queueName, '', false, true, false, false, $callback);
 
        while (count(self::$channel->callbacks)) {
            self::$channel->wait();
        }
    }
 
    /**
     * 销毁
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        self::$channel->close();
        self::$connection->close();
    }
}