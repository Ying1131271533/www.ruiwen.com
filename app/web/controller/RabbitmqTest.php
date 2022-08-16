<?php

namespace app\web\controller;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use app\Request;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use think\facade\Log;

class RabbitmqTest
{
    protected $config = array(
        'host'     => '192.168.0.184',
        // 'host'     => 'rabbitmq',
        'vhost'    => '/',
        'port'     => 5672,
        'login'    => 'akali',
        'password' => '123456',
    );

    public function __construct()
    {}

    public function index()
    {
        $conn = new \AMQPConnection(config('app.rabbitmq'));
        // halt($conn->getHost());
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        halt($conn->connect());
        return success('神织恋');
    }

    // 生产者 详细描述
    public function publisher(Request $request)
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
        $queue = 'hello';
        // 测试镜像
        $channel->queue_declare($queue, false, true, false, true);
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
        // 参数3:没有声明交换机时，这里填队列名称，而不是路由
        // 参数4:传递消息额外设置
        $channel->basic_publish($amqpMsg, '', $queue);

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        return success("Send Message: " . $msg);
    }

    // 工作队列
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
        // 参数4：是否开启交换机持久化
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

    // 直连
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

    // 主题 动态路由
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

    // 发布确认 单个 批量 异步确认
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

    // 死信队列 - 可以保证消息不会丢失
    public function dead(Request $request)
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
        $routing_key = 'routing_key';
        // 设置死信时间10s过期，等待10秒未进行消费，数据会自动跑去死信队列中(还真跑到死信队列了)
        $ttl = 10000;

        // 死信队列名称
        $dead_queue = 'dead_queue';
        // 死信交换机名称
        $dead_exchange = 'dead_exchange';
        // 死信routing_key
        $dead_routing_key = 'dead_routing_key';

        /*********************  死信队列  *********************/

        // 声明死信交换机和队列
        $channel->exchange_declare($dead_exchange, 'direct', false, false, false);
        $channel->queue_declare($dead_queue, false, true, false, false);
        $channel->queue_bind($dead_queue, $dead_exchange, $dead_routing_key);

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
            'x-max-length'              => 6,
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
        $channel->queue_bind($normal_queue, $normal_exchange, $routing_key);

        /*********************  发送消息  *********************/

        // 接受数据
        $msg = $request->params['msg'];
        /* // amqp对象，消息持久化
        $amqpMsg = new AMQPMessage($msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);

        // 发送消息
        $channel->basic_publish($amqpMsg, $normal_exchange, $routing_key); */

        // 模拟第二种死信情况 长度限制
        for ($i = 1; $i < 11; $i++) {
            $amqpMsg = new AMQPMessage($i, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
            $channel->basic_publish($amqpMsg, $normal_exchange, $routing_key);
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($connection, $channel);

        // 返回
        return success('Send Message: ' . $msg);
    }

    // 延迟队列
    public function delay(Request $request)
    {
        // 接收数据
        $msg = $request->params['msg'];
        // 保存到日志
        Log::info("发一条信息给两个延迟(TTL)队列: " . $msg);

        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();

        // 普通交换机名称
        $normal_exchange = 'normal_exchange';
        // 普通队列名称A和B
        $queue_a = 'queue_a';
        $queue_b = 'queue_b';
        // 普通routing_key A
        $routing_key_a = 'routing_key_a';
        // 普通routing_key B
        $routing_key_b = 'routing_key_b';

        // 延迟交换机名称
        $delay_exchange = 'delay_exchange';
        // 延迟队列名称
        $delay_queue = 'delay_queue';
        // 延迟routing_key
        $delay_routing_key = 'delay_routing_key';

        // 普通队列名称C 用于优化队列
        $queue_c = 'queue_c';
        // 普通routing_key C
        $routing_key_c = 'routing_key_c';

        // 延迟时间A
        $ttl_a = 10000;
        // 延迟时间B
        $ttl_b = 40000;

        // 声明普通交换机
        $channel->exchange_declare($normal_exchange, 'direct', false, false, false);

        // 设置队列A的中数据存活时间、延迟队列、延迟路由key
        $arguments = new AMQPTable([
            // 设置TTL过期时间
            'x-message-ttl'             => $ttl_a,
            // 设置延迟队列
            'x-dead-letter-exchange'    => $delay_exchange,
            // 设置延迟routing_key
            'x-dead-letter-routing-key' => $delay_routing_key,
        ]);

        // 声明普通队列A
        $channel->queue_declare($queue_a, false, true, false, false, false, $arguments);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($queue_a, $normal_exchange, $routing_key_a);

        // 设置队列B的中数据存活时间、延迟队列、延迟路由key
        /* $arguments = new AMQPTable([
        // 设置TTL过期时间
        'x-message-ttl' => $ttl_b,
        // 设置延迟队列
        'x-delay-letter-exchange' => $delay_exchange,
        // 设置延迟routing_key
        'x-delay-routing-key' => $delay_routing_key
        ]); */
        $arguments->set('x-message-ttl', $ttl_b);
        // 声明普通队列B
        $channel->queue_declare($queue_b, false, true, false, false, false, $arguments);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($queue_b, $normal_exchange, $routing_key_b);

        // 设置队列C的中数据延迟队列、延迟路由key
        $argumentsC = new AMQPTable([
            // 设置延迟队列
            'x-dead-letter-exchange'    => $delay_exchange,
            // 设置延迟routing_key
            'x-dead-letter-routing-key' => $delay_routing_key,
        ]);
        // 声明普通队列C 用于优化队列
        $channel->queue_declare($queue_c, false, true, false, false, false, $argumentsC);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($queue_c, $normal_exchange, $routing_key_c);

        // 延迟交换机
        $channel->exchange_declare($delay_exchange, 'direct', false, false, false);
        // 延迟队列
        $channel->queue_declare($delay_queue, false, true, false, false);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($delay_queue, $delay_exchange, $delay_routing_key);

        // amqp对象A
        $amqpMsgA = new AMQPMessage(date('Y-m-d H:i:s') . ' 消息来自ttl为10s的A队列: ' . $msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        // 发送A
        $channel->basic_publish($amqpMsgA, $normal_exchange, $routing_key_a);

        // amqp对象B
        $amqpMsgB = new AMQPMessage(date('Y-m-d H:i:s') . ' 消息来自ttl为40s的B队列: ' . $msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        // 发送B
        $channel->basic_publish($amqpMsgB, $normal_exchange, $routing_key_b);

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        return success('发送消息: ' . $msg);
    }

    // 延迟队列优化 - 单个消息延迟 有缺点 先进先出
    public function delay_optimization(Request $request)
    {
        // 消息
        $msg = $request->params['msg'];
        // 延迟时间
        $ttl = $request->params['ttl_time'];

        // 保存到日志
        $log_msg = " 发一条信息给时长为" . ($ttl / 1000) . "s的延迟(TTL)队列C: " . $msg;
        Log::info($log_msg);

        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();

        // 普通交换机名称
        $normal_exchange = 'normal_exchange';
        // 普通队列名称C 用于优化队列
        $queue_c = 'queue_c';
        // 普通routing_key C
        $routing_key_c = 'routing_key_c';

        // 延迟交换机名称
        $delay_exchange = 'delay_exchange';
        // 延迟队列名称
        $delay_queue = 'delay_queue';
        // 延迟routing_key
        $delay_routing_key = 'delay_routing_key';

        // 声明普通交换机
        $channel->exchange_declare($normal_exchange, 'direct', false, false, false);

        // 设置队列C的中数据延迟队列、延迟路由key
        $argumentsC = new AMQPTable([
            // 设置延迟队列
            'x-dead-letter-exchange'    => $delay_exchange,
            // 设置延迟routing_key
            'x-dead-letter-routing-key' => $delay_routing_key,
        ]);
        // 声明普通队列C 用于优化队列
        $channel->queue_declare($queue_c, false, true, false, false, false, $argumentsC);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($queue_c, $normal_exchange, $routing_key_c);

        // 延迟交换机
        $channel->exchange_declare($delay_exchange, 'direct', false, false, false);
        // 延迟队列
        $channel->queue_declare($delay_queue, false, true, false, false);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($delay_queue, $delay_exchange, $delay_routing_key);

        // amqp对象，这是用使用单条消息过期时间，而不是使用队列过期时间
        // 老师演示的时候发现这种设置消息的方式，会有先进先出问题，就算第二条消息过期时间
        // 比第一条消息过期时间少，也是得等第一条消息出来之后，才会跟着出来
        // 因为队列是先进先出的，所以在队列中先进入的过期时间为20秒的A消息
        // 把后进入的过期时间为2秒的B消息阻塞在了后面，所以B消息出不来
        $amqpMsg = new AMQPMessage(
            date('Y-m-d H:i:s') . ' 消息来自ttl为' . ($ttl / 1000) . 's的C优化队列: ' . $msg,
            [
                // 消息持久化
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
                // 设置消息过期时间
                'expiration'    => $ttl,
            ]
        );
        // 发送
        $channel->basic_publish($amqpMsg, $normal_exchange, $routing_key_c);

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        return success('发送消息: ' . $log_msg);
    }

    // 延迟队列 - 延迟队列插件
    public function delayed(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取信道
        $channel = $connection->channel();

        // 延迟队列交换姬
        $delayed_exchange = 'delayed_exchange';
        // 延迟队列名称
        $delayed_queue = 'delayed_queue';
        // 延迟队列路由键
        $delayed_routing_key = 'delayed_routing_key';

        // 设置延迟交换机的队列类型为direct，也可以选topic和其它的交换机类型
        $arguments = new AMQPTable(['x-delayed-type' => 'direct']);
        $channel->exchange_declare($delayed_exchange, 'x-delayed-message', false, true, false, false, false, $arguments);

        // 声明队列
        $channel->queue_declare($delayed_queue, false, true, false, false, false);
        // 将队列名与交换机名进行绑定，并指定routing_key
        $channel->queue_bind($delayed_queue, $delayed_exchange, $delayed_routing_key);

        // 获取消息
        $msg = $request->params['msg'];
        // 获取延迟时间
        $ttl = $request->params['ttl'];

        // 保存到日志
        $log_msg = "发一条信息给时长为" . ($ttl / 1000) . "s的延迟插件(TTL)队列: " . $msg;
        Log::info($log_msg);

        // 创建消息
        $amqpMsg = new AMQPMessage(
            date('Y-m-d H:i:s') . ' 消息来自ttl为' . ($ttl / 1000) . 's的插件优化队列: ' . $msg,
            [
                // 消息持久化
                'delivery_mode'       => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
                // 设置过期时间
                'application_headers' => new AMQPTable(['x-delay' => $ttl]),
            ]
        );

        // 发送
        $channel->basic_publish($amqpMsg, $delayed_exchange, $delayed_routing_key);

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        // 返回
        return success('发送消息: ' . $log_msg);
    }

    // 发布确认 - 高级 消息回退
    // 问题: jinx的消息也能被akali消费？
    public function confirm_high(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取消息通道
        $channel = $connection->channel();

        // 交换姬名称
        $confirm_exchange = 'confirm_exchange';
        // 队列名称
        $confirm_queue = 'confirm_queue';
        // 路由键
        $confirm_routing_key = 'akali';
        // $confirm_routing_key = 'jinx';

        // 获取消息
        $msg = $request->params['msg'];
        // 获取routing_key
        // $confirm_routing_key  = $request->params['routing_key'];

        // 声明交换姬
        $channel->exchange_declare($confirm_exchange, 'direct', false, true, false);
        // 声明队列
        $channel->queue_declare($confirm_queue, false, true, false, false);
        // 将交换姬和队列进行绑定，并且指定routing_key
        $channel->queue_bind($confirm_queue, $confirm_exchange, $confirm_routing_key);

        // 创建消息
        $amqpMsg = new AMQPMessage($msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        // 开启发布确认
        $channel->confirm_select();
        // 成功到达交换姬时执行
        $channel->set_ack_handler(function (AMQPMessage $msg) {
            // 到不了交换姬的不知道怎么模拟
            // echo '成功到达交换机: ' . $msg->body . PHP_EOL;
            // mandatory为true的时候就是这种
            echo '消息成功进入队列: ' . $msg->body . PHP_EOL;
        });
        // rabbitmq内部错误时触发
        $channel->set_nack_handler(function (AMQPMessage $msg) {
            echo 'rabbitmq内部错误: ' . $msg->body . PHP_EOL;
        });
        // 消息到达交换机,但是没有进入合适的队列,消息回退
        /* $channel->set_return_listener(
        function ($reply_code, $reply_text, $exchange, $routing_key, AMQPMessage $msg) {
        echo '没有进入合适的队列，消息回退' . PHP_EOL;
        }
        ); */
        //消息到达交换机,但是没有进入合适的队列,消息回退
        $channel->set_return_listener(function (
            $reply_code,
            $reply_text,
            $exchange,
            $routing_key,
            AMQPMessage $msg
        ) use (
            $channel,
            $connection,
            $amqpMsg,
            $confirm_exchange,
            $confirm_routing_key
        ) {
            // 打印消息
            // $log_reply = "消息被交换机退回，入队失败 - 响应码: $reply_code 响应文本: $reply_text 交换机: $exchange 路由键: $routing_key 消息: $msg->body";
            $log_reply = "消息: {$msg->body}，被交换机{$exchange}退回，退回原因是：{$reply_text}，路由Key：{$routing_key} \n";
            echo $log_reply;
            // 保存日志
            \think\facade\Log::error($log_reply);

            // 重新发布
            $channel->basic_publish($amqpMsg, $confirm_exchange, $confirm_routing_key, true);

            // 保存到日志
            $log_msg = "触发了消息退回，再次发布消息给路由键为{$routing_key}的队列: $msg->body \n";
            echo $log_msg;
            $ruiwen = \think\facade\Log::info($log_msg);

            // 关闭连接，这里需要关闭连接，是因为使用了消息阻塞，还没跑到关闭连接就被消息退回拦截了
            $channel->close();
            $connection->close();
            exit;
        });

        // 模拟消息退回

        // 到不了交换姬的不知道怎么模拟，老师视频中可以设置消息到不了交换机就返回消息给生产者
        // 连接参数是这个 spring.rabbitmq.publisher-confirm-type=correlated
        // 以后看看php要怎么弄

        // 绑定交换姬的routing_key是akali，而发布消息时的routing_key是jinx，这样消息就路由不到队列了
        $confirm_routing_key = 'jinx';

        // 发布消息
        // 参数3：生产者发布消息时设置 mandatory=true,表示消息无法路由到队列时,会退回给生产者
        // 老师：可以在消息传递过程中，达不到目的地时，将消息返回给生产者
        // 必须要设置 mandatory=true 不然模拟不了消息退回
        $channel->basic_publish($amqpMsg, $confirm_exchange, $confirm_routing_key, true);

        // 阻塞，等待消息确认
        $channel->wait_for_pending_acks_returns();

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        // 保存到日志
        $log_msg = "发一条信息给发布确认高级的队列: " . $msg;
        Log::info($log_msg);

        // 返回
        return success($log_msg);
    }

    // 发布确认 - 高级 备用交换机 alternate-exchange (有点像死信队列)
    // 问题: 到不了交换姬的消息回退不知道怎么模拟
    public function confirm_backup(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取消息通道
        $channel = $connection->channel();

        // 交换姬名称
        $confirm_exchange = 'confirm_exchange';
        // 队列名称
        $confirm_queue = 'confirm_queue';
        // 路由键
        $confirm_routing_key = 'akali';
        // $confirm_routing_key = 'jinx';

        // 备用交换机
        $backup_exchange = 'backup_exchange';
        // 备用队列
        $backup_queue = 'backup_queue';
        // 警告队列
        $warning_queue = 'warning_queue';

        // 获取消息
        $msg = $request->params['msg'];

        // 设置交换机的备用交换机
        $arguments = new AMQPTable(['alternate-exchange' => $backup_exchange]);
        // 声明交换姬
        $channel->exchange_declare($confirm_exchange, 'direct', false, true, false, false, false, $arguments);
        // 声明队列
        $channel->queue_declare($confirm_queue, false, true, false, false);
        // 将交换姬和队列进行绑定，并且指定routing_key
        $channel->queue_bind($confirm_queue, $confirm_exchange, $confirm_routing_key);

        // 声明备用交换机 注意！：这里是同时发给备份消费者和警告消费者
        $channel->exchange_declare($backup_exchange, 'fanout', false, true, false);

        // 声明备份队列
        $channel->queue_declare($backup_queue, false, true, false, false);
        // 将备份交换机和备份队列进行绑定
        $channel->queue_bind($backup_queue, $backup_exchange);

        // 声明警告队列
        $channel->queue_declare($warning_queue, false, true, false, false);
        // 将备份交换机和警告队列进行绑定
        $channel->queue_bind($warning_queue, $backup_exchange);

        // 创建消息
        $amqpMsg = new AMQPMessage($msg, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        // 开启发布确认
        $channel->confirm_select();
        // 成功到达队列时执行，如果是成功进入了备用交换机的队列时，也会触发这里
        $channel->set_ack_handler(function (AMQPMessage $msg) {
            echo '消息成功进入队列: ' . $msg->body . PHP_EOL;
        });
        // rabbitmq内部错误时触发
        $channel->set_nack_handler(function (AMQPMessage $msg) {
            echo 'rabbitmq内部错误: ' . $msg->body . PHP_EOL;
        });
        // 消息到达交换机,但是没有进入合适的队列,消息回退
        // 注意！如果设置了备用交换机 alternate-exchange ，那么优先选择备用交换机，而不是消息回退
        $channel->set_return_listener(function (
            $reply_code,
            $reply_text,
            $exchange,
            $routing_key,
            AMQPMessage $msg
        ) use (
            $channel,
            $connection,
            $amqpMsg,
            $confirm_exchange,
            $confirm_routing_key
        ) {
            // 打印消息
            // $log_reply = "消息被交换机退回，入队失败 - 响应码: $reply_code 响应文本: $reply_text 交换机: $exchange 路由键: $routing_key 消息: $msg->body";
            $log_reply = "消息: {$msg->body}，被交换机{$exchange}退回，退回原因是：{$reply_text}，路由Key：{$routing_key} \n";
            echo $log_reply;
            // 保存日志
            \think\facade\Log::error($log_reply);

            // 重新发布 这里是要发给备份交换机的，既是别的服务器里面
            // 老师视频好像是交换机类型是fanout
            // 为了测试就发到本机的rabbitmq了
            // $channel->exchange_declare('backup_exchange', 'direct', false, true, false);
            // $channel->queue_declare('back_queue', false, true, false, false);
            // $channel->queue_bind('back_queue', 'backup_exchange', 'backup_routing_key');
            // $channel->basic_publish($amqpMsg, 'backup_exchange', 'backup_routing_key');
            $channel->basic_publish($amqpMsg, $confirm_exchange, $confirm_routing_key);

            // 保存到日志
            $log_msg = "触发了消息退回，再次发布消息给路由键为{$routing_key}的队列: $msg->body \n";
            echo $log_msg;
            \think\facade\Log::info($log_msg);

            // 关闭连接，这里需要关闭连接，是因为使用了消息阻塞，还没跑到关闭连接就被消息退回拦截了
            $channel->close();
            $connection->close();
            exit;
        });

        // 模拟消息回退

        // 到不了交换姬的不知道怎么模拟，老师视频中可以设置消息到不了交换机就返回消息给生产者
        // 连接参数是这个 spring.rabbitmq.publisher-confirm-type=correlated
        // 以后看看php要怎么弄

        // 绑定交换姬的routing_key是akali，而发布消息时的routing_key是jinx，这样消息就路由不到队列了
        $confirm_routing_key = 'jinx';

        // 发布消息
        // 参数4：生产者发布消息时设置 mandatory=true,表示消息无法路由到队列时,会退回给生产者
        // 老师：可以在消息传递过程中，达不到目的地时，将消息返回给生产者
        // 必须要设置 mandatory=true 不然模拟不了消息退回
        $channel->basic_publish($amqpMsg, $confirm_exchange, $confirm_routing_key, true);

        // 阻塞，等待消息确认
        $channel->wait_for_pending_acks_returns();

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        // 保存到日志
        $log_msg = "发一条信息给发布确认高级的队列: " . $msg;
        Log::info($log_msg);

        // 返回
        return success($log_msg);
    }

    // 优先队列
    public function priority_queue(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection();
        // 获取信道
        $channel = $connection->channel();

        // 优先级交换机名称
        $priority_queue = 'priority_exchange';
        // 优先级队列名称
        $priority_queue = 'priority_queue';

        // 设置队列的优先级参数，优先级最大为10
        $arguments = new AMQPTable(['x-max-priority' => 10]);
        // 还可以这样？
        // $arguments = new AMQPTable(['x-max-priority' => ['I', 100]]);
        // 声明队列
        $channel->queue_declare($priority_queue, false, true, false, false, false, $arguments);

        // 获取数据
        $msg = $request->params['msg'];

        // 发布消息
        for ($i = 1; $i <= 1000000; $i++) {

            // 创建消息
            $amqpMsg = new AMQPMessage($i);
            // $amqpMsg = new AMQPMessage($msg, ['priority' => 10]);

            // 如果i等于6的时候，就赋予优先级10，当然也可以是2，随意
            if ($i == 6) {
                $amqpMsg->setBody($i . ' - 优先级为8');
                $amqpMsg->set('priority', 8);
            }

            if ($i == 8) {
                $amqpMsg->setBody($i . ' - 优先级为10');
                $amqpMsg->set('priority', 10);
            }

            // 发布
            $channel->basic_publish($amqpMsg, '', $priority_queue);
        }

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        // 返回
        return success('优先级队列发布消息: ' . $msg);
    }

    // 惰性队列 - 老师这里没有演示
    // 实现方式：
    // 可以设置普通队列的最大长度为10000条，多出的消息就转发到死信队列里面
    // 而这个死信队列设置的就是lazy模式
    public function lazy_queue(Request $requst)
    {
        // 获取连接对象
        $connection = RabbitMqConnection::getConnection();
        // 获取通道
        $channel = $connection->channel();

        // 惰性队列名称
        $lazy_queue = 'lazy_queue';

        // 声明死信队列，设置为惰性队列
        $arguments = new AMQPTable(['x-queue-mode' => 'lazy']);
        $channel->queue_declare($lazy_queue, false, true, false, false, false, $arguments);

    }

    // 测试镜像集群和Federation
    public function mirror(Request $request)
    {
        // 获取连接
        $connection = RabbitMqConnection::getConnection(['host' => '192.168.159.128']);

        // 获取连接中通道
        $channel = $connection->channel();
        
        // 声明Federation交换机
        $fed_exchange = 'fed_exchange';
        // 声明队列
        $queue = 'mirror_hello';
        
        // 声明Federation交换机
        $channel->exchange_declare($fed_exchange, 'direct', false, false, false);
        $channel->queue_declare($queue, false, true, false, true);
        // 接收消息参数
        $msg = $request->params['msg'];

        // 生成消息
        $amqpMsg = new AMQPMessage($msg);

        // 发布消息
        $channel->basic_publish($amqpMsg, '', $queue);

        // 关闭连接
        RabbitMqConnection::closeConnectionAndChannel($channel, $connection);

        return success("Send Message: " . $msg);
    }
}
