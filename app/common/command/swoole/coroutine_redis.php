<?php

// redis的协程测试

use function Swoole\Coroutine\run;
use Swoole\Coroutine;
use Swoole\Runtime;

// 需要先创建redis数据 a:'阿卡丽'
class Akali
{
    // 使用协程
    // 这里测试方式是，同时打开两个这里的程序，看看第二个会不会被阻塞
    public function go()
    {
        // 此行代码后，文件操作，sleep，Mysqli，PDO，streams等都变成异步IO，见'一键协程化'章节
        Runtime::enableCoroutine();

        run(function () {

            $redis = new \Redis();
            $redis->connect('redis', 6379); //此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
            $password = 'Ym-12]i4!gDal^Jc/3@n.c^Mh';
            $redis->auth($password);
            $res = $redis->get('a'); // 此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
            var_dump($res);
            sleep(5);
            echo '阿卡丽';

            $redis->close();
        });
    }
}

$mysql = new Akali();
$mysql->go();
