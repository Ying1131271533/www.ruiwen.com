<?php
/**
 * 接收(工作队列)
 * @param $callback
 */
 
namespace app\common\command;

use app\common\lib\classes\rabbitmq\RabbitMqWork;
use think\console\Command;
use think\console\Input;
use think\console\Output;
 
class WorkQueue extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('workQueue');
    }
 
    protected function execute(Input $input, Output $output)
    {
        $RabbitMqWork = new RabbitMqWork();
        $callback = function ($msg){
            // echo " [x] 消费者-1：", $msg->body, "\n";
            echo " [x] 消费者-2： ", $msg->body, "\n";
            // echo " [x] Received ", $msg->body, "\n";
            // sleep(substr_count($msg->body, '.'));
            // sleep(1);
            echo " [x] Done", "\n";
            // 如果有业务需求，就使用下面的消息确认，不需要在basic_consume的no_ack赋值为true
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $RabbitMqWork->workTask($callback);
    }
}