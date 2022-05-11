<?php

namespace lib;

// 一致性哈希算法分区 - 虚拟节点
class ConsistentHash
{
    protected $nodes   = array();
    protected $postion = array();
    // protected $mul = 32; // 每个节点对应 32 个虚节点
    protected $mul = 64; // 每个节点对应 64 个虚节点，节点树越多，缓存分布得越均匀

    public function hash($str)
    {
        return sprintf('%u', crc32($str)); // 把字符串转成 32 位符号整数
    }

    // 查找key落到那个节点上
    public function findNode($key)
    {
        $point = $this->hash($key);
        $node  = current($this->postion); // 先取圆环上最小的一个节点，既第一个节点，因为数组已经排序过了
        foreach ($this->postion as $k => $v) {
            if ($point <= $k) {
                $node = $v;
                break;
            }
        }

        reset($this->postion); //复位数组指针
        return $node; //$key哈希后比最大的节点都大 就放到第一个节点

    }

    // 添加节点
    public function addNode($node)
    {
        if (isset($this->nodes[$node])) {
            return;
        }

        for ($i = 0; $i < $this->mul; $i++) {
            $pos                  = $this->hash($node . '-' . $i); //$node = '168.10.1.72:8888'
            $this->postion[$pos]  = $node;
            $this->nodes[$node][] = $pos; //方便删除对应的虚拟节点
        }

        $this->sortPos();

    }

    /**
     * 删除某台redis服务器的所有节点
     *
     * @param  string      $node    服务器redis的ip和端口，例如：127.0.0.1:11212
     */
    public function delNode($node)
    {
        if (!isset($this->nodes[$node])) {
            return;
        }

        foreach ($this->nodes[$node] as $k) {
            unset($this->postion[$k]); //删除对应的虚节点
        }

        unset($this->nodes[$node]);
    }

    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/04/06 15:57
     *
     * 获取节点的对应服务器连接数据
     *
     * @param  string       $key                键名
     * @param  array        $serverConfArr      服务器群的连接数组
     * @return array                            返回对应的服务器ip、端口
     */
    public function connect($key, $serverConfArr = [])
    {
        //比如配置文件 $memServerConfArr = ['168.10.1.7:5566','168.10.1.2:7788','168.10.1.72:8899']
        foreach ($serverConfArr as $mem_config) {
            $this->addNode($mem_config); // 添加节点
        }

        $memNode = $this->findNode($key);
        // echo($memNode);die(); // 测试落到 127.0.0.1:11214 这个节点上

        $mem  = explode(':', $memNode);
        $host = $mem[0];
        $port = $mem[1];

        return ['host' => $host, 'port' => $port];
    }

    // 对数组按照键名进行升序排序
    protected function sortPos()
    {
        ksort($this->postion, SORT_REGULAR); // SORT_REGULAR - 正常比较单元（不改变类型）
    }

}

/*

// 使用测试-----start

// 连接
$con = new ConsistentHash();

//比如配置文件 $memServerConfArr = ['168.10.1.7:5566','168.10.1.2:7788','168.10.1.72:8899']
$memServerConfArr = array('127.0.0.1:11212', '127.0.0.1:11213', '127.0.0.1:11214');
foreach ($memServerConfArr as $mem_config) {
$con->addNode($mem_config); //添加节点

}

$key = 'jinx';
$memNode = $con->findNode($key);
//echo($memNode);die();  // 测试落到 127.0.0.1:11214 这个节点上

$mem = explode(':', $memNode);
$host = $mem[0];
$port = $mem[1];

$memcache = new Memcache();
$memcache->connect($host, $port);
$result = $memcache->set($key, '爆爆');
// $result = $memcache->get($key);

//------------end

 */
