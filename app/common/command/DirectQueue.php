<?php
/**
 * 接收(直接交换机)
 * @param \Closure $callback
 * @param array $bindingKeys
 */

namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMq;
use app\common\lib\classes\rabbitmq\RabbitMqWork;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class DirectQueue extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('directQueue');
    }

    protected function execute(Input $input, Output $output)
    {
        $RabbitMqWork = new RabbitMqWork(RabbitMq::DIRECT);
        $callback     = function ($msg) {
            // echo "[x] 消费者1-" . $msg->delivery_info['routing_key'] . ":$msg->body \n";
            // echo "[x] 消费者2-" . $msg->delivery_info['routing_key'] . ":$msg->body \n";
            echo "[x] 消费者3-" . $msg->delivery_info['routing_key'] . ":$msg->body \n";
            echo "[x] " . $msg->delivery_info['routing_key'] . ":$msg->body \n";
            // 保存到日志，这里可以用于日后错误日志的保存
            Log::error("Msg: $msg->body");
        };
        $RabbitMqWork->receiveDirect($callback, RabbitMq::SEVERITYS);
    }
}