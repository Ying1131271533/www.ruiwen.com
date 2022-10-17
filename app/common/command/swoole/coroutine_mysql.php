<?php

// mysql的协程测试

use function Swoole\Coroutine\run;
use Swoole\Coroutine;
use Swoole\Runtime;

// 需要先创建数据库swoole，再创建test表，插入一条数据：id=1 test=阿卡丽
class MySQL
{
    // 测试不用下面那种使用协程的方式运行
    // 确实是执行完了第一个for才会执行第二个for
    // 用时：1.4247059822083 s
    public function common()
    {
        $s = microtime(true);
        for ($c = 50; $c--;) {
            $pdo       = new \PDO('mysql:host=mysql;dbname=swoole;charset=utf8', 'root', 'Hw]al^049cAa83K4sK/3@n.bd');
            $statement = $pdo->prepare('SELECT `id` FROM `test` where id = 1');
            for ($n = 100; $n--;) {
                $statement->execute();
                $res = $statement->fetch();
                var_dump('a：' . $res['test']);
            }
        }

        for ($c = 50; $c--;) {
            $mysqli = mysqli_connect('mysql', 'root', 'Hw]al^049cAa83K4sK/3@n.bd', 'swoole');
            for ($n = 100; $n--;) {
                $temp = mysqli_query($mysqli, 'SELECT `id` FROM `test` where id = 1');
                $res  = mysqli_fetch_array($temp);
                var_dump('b：' . $res['test']);
            }
        }

        echo '普通方式用时：' . (microtime(true) - $s) . ' s' . PHP_EOL;
    }

    // 使用协程
    // 用时：1.1094260215759 s
    public function go()
    {
        // 此行代码后，文件操作，sleep，Mysqli，PDO，streams等都变成异步IO，见'一键协程化'章节
        Runtime::enableCoroutine();
        $s = microtime(true);

        // Swoole\Coroutine\run()见'协程容器'章节
        run(function () {
            // 测试结果：
            // 打印出来的数据中，a数据和b数据是有部分混合在一起显示的
            // 所以，第二个for不会等第一个for运行完毕，再运行，加快了程序速度
            for ($c = 50; $c--;) {
                Coroutine::create(function () {
                    $pdo       = new \PDO('mysql:host=mysql;dbname=swoole;charset=utf8', 'root', 'Hw]al^049cAa83K4sK/3@n.bd');
                    $statement = $pdo->prepare('SELECT `id` FROM `test` where id = 1');
                    for ($n = 100; $n--;) {
                        $statement->execute();
                        $res = $statement->fetch();
                        var_dump('a：' . $res['test']);
                    }
                });
            }

            for ($c = 50; $c--;) {
                Coroutine::create(function () {
                    $mysqli = mysqli_connect('mysql', 'root', 'Hw]al^049cAa83K4sK/3@n.bd', 'swoole');
                    for ($n = 100; $n--;) {
                        $temp = mysqli_query($mysqli, 'SELECT `id` FROM `test` where id = 1');
                        $res  = mysqli_fetch_array($temp);
                        var_dump('b：' . $res['test']);
                    }
                });
            }
        });

        echo '协程方式用时：' . (microtime(true) - $s) . ' s' . PHP_EOL;
    }
}

$mysql = new MySQL();
$mysql->common();
// $mysql->go();
