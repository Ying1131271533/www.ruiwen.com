<?php

// swoole的协程测试

use function Swoole\Coroutine\run;
use Swoole\Coroutine;
use Swoole\Runtime;

// 需要先创建数据库swoole，再创建test表，插入一条数据：id=1 test=阿卡丽
class Akali
{
    public function go()
    {
        // 此行代码后，文件操作，sleep，Mysqli，PDO，streams等都变成异步IO，见'一键协程化'章节
        Runtime::enableCoroutine();
        $s = microtime(true);

        // Swoole\Coroutine\run()见'协程容器'章节
        run(function () {
            // i just want to sleep...
            for ($c = 100; $c--;) {
                Coroutine::create(function () {
                    for ($n = 100; $n--;) {
                        usleep(1000);
                    }
                });
            }

            // 10k file read and write
            for ($c = 100; $c--;) {
                Coroutine::create(function () use ($c) {
                    $tmp_filename = "/tmp/test-{$c}.php";
                    for ($n = 100; $n--;) {
                        $self = file_get_contents(__FILE__);
                        file_put_contents($tmp_filename, $self);
                        assert(file_get_contents($tmp_filename) === $self);
                    }
                    unlink($tmp_filename);
                });
            }

            // 10k pdo and mysqli read
            for ($c = 50; $c--;) {
                Coroutine::create(function () {
                    $pdo       = new PDO('mysql:host=mysql;dbname=swoole;charset=utf8', 'root', 'Hw]al^049cAa83K4sK/3@n.bd');
                    $statement = $pdo->prepare('SELECT * FROM `test`');
                    for ($n = 100; $n--;) {
                        $statement->execute();
                        assert(count($statement->fetchAll()) > 0);
                    }
                });
            }
            for ($c = 50; $c--;) {
                Coroutine::create(function () {
                    $mysqli    = new Mysqli('mysql', 'root', 'Hw]al^049cAa83K4sK/3@n.bd', 'swoole');
                    $statement = $mysqli->prepare('SELECT `id` FROM `test`');
                    for ($n = 100; $n--;) {
                        $statement->bind_result($id);
                        $statement->execute();
                        $statement->fetch();
                        assert($id > 0);
                    }
                });
            }

            // php_stream tcp server & client with 12.8k requests in single process
            function tcp_pack(string $data): string
            {
                return pack('n', strlen($data)) . $data;
            }

            function tcp_length(string $head): int
            {
                return unpack('n', $head)[1];
            }

            Coroutine::create(function () {
                $ctx    = stream_context_create(['socket' => ['so_reuseaddr' => true, 'backlog' => 128]]);
                $socket = stream_socket_server(
                    'tcp://0.0.0.0:9502',
                    $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $ctx
                );
                if (!$socket) {
                    echo "{$errstr} ({$errno})\n";
                } else {
                    $i = 0;
                    while ($conn = stream_socket_accept($socket, 1)) {
                        stream_set_timeout($conn, 5);
                        for ($n = 100; $n--;) {
                            $data = fread($conn, tcp_length(fread($conn, 2)));
                            assert($data === "Hello Swoole Server #{$n}!");
                            fwrite($conn, tcp_pack("Hello Swoole Client #{$n}!"));
                        }
                        if (++$i === 128) {
                            fclose($socket);
                            break;
                        }
                    }
                }
            });
            for ($c = 128; $c--;) {
                Coroutine::create(function () {
                    $fp = stream_socket_client('tcp://127.0.0.1:9502', $errno, $errstr, 1);
                    if (!$fp) {
                        echo "{$errstr} ({$errno})\n";
                    } else {
                        stream_set_timeout($fp, 5);
                        for ($n = 100; $n--;) {
                            fwrite($fp, tcp_pack("Hello Swoole Server #{$n}!"));
                            $data = fread($fp, tcp_length(fread($fp, 2)));
                            assert($data === "Hello Swoole Client #{$n}!");
                        }
                        fclose($fp);
                    }
                });
            }

            // udp server & client with 12.8k requests in single process
            Coroutine::create(function () {
                $socket = new \Swoole\Coroutine\Socket(AF_INET, SOCK_DGRAM, 0);
                $socket->bind('127.0.0.1', 9503);
                $client_map = [];
                for ($c = 128; $c--;) {
                    for ($n = 0; $n < 100; $n++) {
                        $recv       = $socket->recvfrom($peer);
                        $client_uid = "{$peer['address']}:{$peer['port']}";
                        $id         = $client_map[$client_uid]         = ($client_map[$client_uid] ?? -1) + 1;
                        assert($recv === "Client: Hello #{$id}!");
                        $socket->sendto($peer['address'], $peer['port'], "Server: Hello #{$id}!");
                    }
                }
                $socket->close();
            });
            for ($c = 128; $c--;) {
                Coroutine::create(function () {
                    $fp = stream_socket_client('udp://127.0.0.1:9503', $errno, $errstr, 1);
                    if (!$fp) {
                        echo "$errstr ($errno)\n";
                    } else {
                        for ($n = 0; $n < 100; $n++) {
                            fwrite($fp, "Client: Hello #{$n}!");
                            $recv                 = fread($fp, 1024);
                            list($address, $port) = explode(':', (stream_socket_get_name($fp, true)));
                            assert($address === '127.0.0.1' && (int) $port === 9503);
                            assert($recv === "Server: Hello #{$n}!");
                        }
                        fclose($fp);
                    }
                });
            }
        });

        echo '用时：' . (microtime(true) - $s) . ' s' . PHP_EOL;
    }
}

$mysql = new Akali();
$mysql->go();
