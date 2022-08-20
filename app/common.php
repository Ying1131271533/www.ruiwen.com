<?php
// 应用公共文件

use think\cache\driver\Memcache;

/**
 * 返回api接口数据
 *
 * @param  string    $smg       描述信息
 * @param  int       $code      http状态码
 * @param  int       $status    程序状态码
 * @param  notype    $data      返回的数据
 * @return json                 api返回的json数据
 */
function show(string $msg, int $code = 200, int $status = 20000, $data = [])
{
    // 组装数据
    $resultData = [
        'status' => $status,
        'msg'    => $msg,
        'data'   => $data,
    ];
    // 返回数据
    return json($resultData, $code);
}

/**
 * 返回成功的api接口数据
 *
 * @param  array|string     $data       返回的数据
 * @param  string           $smg        描述信息
 * @param  int              $status     程序状态码
 * @param  int              $code       http状态码
 * @return json                         api返回的json数据
 */
function success($data = [], int $status = 20000, int $code = 200, string $msg = '成功')
{
    // 组装数据
    if (is_string($data) && (int) ($data) == 0) {
        $resultData = [
            'status' => $status,
            'msg'    => $data,
            'data'   => [],
        ];
    } else {
        $resultData = [
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data,
        ];
    }
    // 返回数据
    // echo json($resultData, $code);exit;
    return json($resultData, $code);
}

/**
 * 返回资源创建成功的api接口数据
 *
 * @param  array|string     $data       返回的数据
 * @param  string           $smg        描述信息
 * @param  int              $status     程序状态码
 * @param  int              $code       http状态码
 * @return json                         api返回的json数据
 */
function create($data = [], int $status = 20001, int $code = 201, string $msg = '成功')
{
    // 组装数据
    $resultData = [
        'status' => $status,
        'msg'    => $msg,
        'data'   => $data,
    ];
    // 返回数据
    return json($resultData, $code);
}

/**
 * 返回失败的api接口数据
 *
 * @param  string    $smg       描述信息
 * @param  int       $status    程序状态码
 * @param  int       $code      http状态码
 * @return json                 api返回的json数据
 */
function fail(string $msg = '失败', int $status = 40000, int $code = 400)
{
    // 组装数据
    $resultData = [
        'status' => $status,
        'msg'    => $msg,
    ];
    // 返回数据
    // echo json_encode($resultData, $code);exit;
    return json($resultData, $code);
}

/**
 * 获取随机字符串
 *
 * @param  int      $length     字符串长度
 * @return json                 pi返回的json数据
 */
function get_rand_char(int $length)
{
    $str    = '';
    $strPol = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    $max    = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip(int $type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        //高级模式获取(防止伪装)
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 获取显示的商品规格值
 *
 * @param array         $data               规格值数组
 * @param string        $field              搜索的key值
 * @param notype        $value              配对的值
 * @return array        $return_data        返回所有配对的数组
 */
function array_filter_value(array $data, string $field, $value)
{
    $return_data = [];
    $return_data = array_filter($data, function ($row) use ($field, $value) {
        if (isset($row[$field]) && $row[$field] == $value) {
            return $row;
        }
    });
    return array_values($return_data);
}

/**
 * curl的GET请求方式
 *
 * @param string    $url    请求链接
 * @return json             返回结果
 */
function curl_get($url)
{
    $header = array(
        'Accept: application/json',
    );
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 超时设置,以秒为单位
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);

    // 超时设置，以毫秒为单位
    // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

    // 设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //执行命令
    $data = curl_exec($curl);

    // 返回信息
    curl_close($curl);
    return $data;
}

/**
 * curl的POST请求方式
 *
 * @param string    $url        请求链接
 * @param string    $postdata   需要传输的数据，数组格式
 * @return json                 返回结果
 */
function curl_post($url, $postdata)
{

    $header = array(
        'Accept: application/json',
    );

    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 超时设置
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

    // 超时设置，以毫秒为单位
    // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

    // 设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    //执行命令
    $data = curl_exec($curl);

    // 返回信息
    curl_close($curl);
    return $data;
}

/**
 * 生成验证码
 *
 * @param  int      $number     验证码个数
 * @return string               生成的验证码
 */
function get_random_number($number = 6): string
{
    $max = pow(10, $number) - 1;
    $min = pow(10, $number - 1);
    return (string) rand($min, $max);
}

// 时间戳转为多少分钟前 几个小时前 几天前
function post_time($time = null)
{
    $text = '';
    $time = $time === null || $time > time() ? time() : intval($time);
    $t    = time() - $time; //时间差 （秒）

    $y = date('Y', $time) - date('Y', time()); //是否跨年
    switch ($t) {
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒前'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟前'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时前'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); // 昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('Y-m-d', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365 && $y == 0:
            $text = date('Y-m-d', $time); //一年内
            break;
        default:
            $text = date('Y-m-d', $time); //一年以前
            break;
    }

    return $text;
}

/**
 * 删除图片
 *
 * @param  string   $path   图片路径
 * @return bool             返回结果
 */
function del_img($path)
{
    $pathurl = '.' . $path;
    if (file_exists($pathurl)) {
        @unlink($pathurl);
    }
}

/**
 * 删除单张旧图片
 *
 * @param  array    $old_image   旧图片路径
 * @return array    $new_image   新图片路径
 */
function del_old_img($old_image, $new_image)
{
    if ($old_image !== $new_image) {
        del_img($old_image);
    }
}

/**
 * 删除多张旧图片
 *
 * @param  array    $data   图片数组
 * @return array    $data   处理好的参数
 */
function del_old_imgs($old_images, $new_images)
{
    $del_images = array_diff($old_images, $new_images);
    foreach ($del_images as $value) {
        del_img($value);
    }
}

/**
 * 删除文件或者文件夹
 *
 * @param  string $dir 文件、文件夹
 * @return type
 */
function delete_dir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }

    $handle = opendir($dir); //打开目录
    while (($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $d = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($d) ? delete_dir($d) : @unlink($d);
    }
    closedir($handle);
    return @rmdir($dir);
}

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/03/31 21:56
 *
 * 递归找子级数据
 *
 * @param  array    $data           二维数组
 * @param  int      $parent_id      父级id
 * @return array                    返回处理好的数组
 */
function get_child(array $array = [], int $parent_id = 0)
{
    $tmp = [];
    foreach ($array as $value) {
        if ($value['parent_id'] == $parent_id) {
            $value['child'] = get_child($array, $value['id']);
            $tmp[]          = $value;
        }
    }
    return $tmp;
}

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/03/31 21:58
 *
 * @param  array    $data           数组
 * @param  int      $parent_id      父级id
 * @param  bool     $spread         节点是否全部展开
 * @return array                    返回处理后的字符串
 */
function get_child_tree_data(array $data = [], int $parent_id = 0, $spread = false)
{
    $tmp = '';
    foreach ($data as $value) {
        if ($value['parent_id'] == $parent_id) {
            $tmp .= "{";
            $tmp .= "label: '{$value['name']}', id: {$value['id']}, pid: {$parent_id},";
            if ($spread) {
                $tmp .= 'spread: true,';
            }

            $child = get_child_tree_data($data, $value['id']);
            if ($child) {
                $tmp .= 'children:[' . $child . ']';
            }

            $tmp .= "},";
        }
    }

    return $tmp;
}

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/04/14 10:45
 *
 * 获取能够连接的memcache服务器
 *
 * @param  array    $serverConfArr  服务器连接配置数组
 * @return array    $serverConfArr  返回连接成功的服务器ip、端口数据
 */
function get_memcache_server($serverConfArr = [])
{
    $serverConfArr or $serverConfArr = config('app.memcache_server');
    foreach ($serverConfArr as $key => $value) {
        $mem      = explode(':', $value);
        $host     = $mem[0];
        $port     = $mem[1];
        $memcache = new Memcache();
        try {
            $memcache->connect($host, $port);
        } catch (\Exception $e) {
            unset($serverConfArr[$key]);
        }
    }

    if (empty($serverConfArr)) {
        return fail('所有memcache服务器都无法连接');
    }

    return $serverConfArr;
}

/**
 * 缓存时间
 *
 * @return  integer     返回时间戳
 */
function cache_time(string $type = 'dawn_rand_time')
{
    switch ($type) {
        // 6小时
        case 'six_hour':
            $time = 3600 * 6;
            break;
        // 12小时 半天
        case 'half_day':
            $time = 3600 * 12;
            break;
        // 一天
        case 'one_day':
            $time = 3600 * 24;
            break;
        // 一周
        case 'one_week':
            $time = 3600 * 24 * 7;
            break;
        // 一个月
        case 'one_month':
            $time = 3600 * 24 * 30;
            break;
        // 一年
        case 'one_year':
            $time = 3600 * 24 * 365;
            break;
        // 随机 3-9 小时
        case 'rand_time':
            $time = rand(3600 * 3, 3600 * 9);
            break;
        // 凌晨0点
        case 'over_day':
            $time = 86400 - (time() + 8 * 3600) % 86400;
            break;
        // 凌晨3点
        case 'dawn_time':
            $time = 86400 - (time() + 8 * 3600) % 86400 + 3600 * 3;
            break;
        // 凌晨3点 + 随机时间
        case 'dawn_rand_time':
            $time = 86400 - (time() + 8 * 3600) % 86400 + 3600 * 3 + rand(1, 3600);
            break;
        // 默认：凌晨3点 + 随机时间
        default:
            $time = 86400 - (time() + 8 * 3600) % 86400 + 3600 * 3 + rand(1, 3600);
            break;
    }

    return $time;
}

/**
 * 返回黎明三点的随机时间
 *
 * @param  integer|string   $id     一般使用数据的id
 * @return integer                  返回距离黎明三点的剩余时间戳
 */
function dawn_time($id)
{
    $number = substr(crc32($id), 6);
    $time = 86400 - (strtotime(date('Y-m-d H:i:30')) + 8 * 3600) % 86400 + 3600 * 3 + (int)$number;
    return $time;
}

// 返回当前的毫秒时间戳
function msectime() {
    list($t1, $t2) = explode(' ', microtime());
    $msectime =  (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    $msectime2 = (string)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    // var_dump("float类型:".$msectime);  // string(29) "float类型:1.60981667462E+12"
    // var_dump("string类型:".$msectime2); // string(26) "string类型:1609816674622"
    return $msectime;
}