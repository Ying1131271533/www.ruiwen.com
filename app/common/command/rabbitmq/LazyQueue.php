<?php

// 惰性队列 - 老师这里没有演示
namespace app\common\command\rabbtimq;

use app\common\lib\classes\rabbitmq\RabbitMqConnection;
use PhpAmqpLib\Wire\AMQPTable;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class LazyQueue extends Command
{
    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('lazy_queue');
    }

    protected function execute(Input $input, Output $output)
    {
        
    }

}