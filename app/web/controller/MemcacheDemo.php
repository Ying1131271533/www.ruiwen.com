<?php
namespace app\web\controller;

use app\common\model\ProductCategory;
use app\common\lib\exception\Error;
use app\common\lib\exception\Fail;
use app\Request;
use think\cache\driver\Memcache;
use think\facade\Cache;

class MemcacheDemo
{

    // 1、统计在线人数
    public function onlineMembers()
    {
        // 连接memcache
        $memcache = new Memcache();

        // 获取在线用户 IP 和 在线时间数据
        $online_members = $memcache->get('online_members');

        // 如果为空，初始化数据
        if (!$online_members) {
            $online_members = [];
        }

        // 获取用户IP
        $ip = $_SERVER["REMOTE_ADDR"];

        // 为访问用户重新设置在线时间
        $online_members[$ip] = time();

        foreach ($online_members as $key => $value) {
            // 如果某个用户在三分钟后再未访问页面，则视为过期
            if (time() - $value > 180) {
                unset($online_members[$key]);
            }
        }

        // 重新设置在线用户数据
        $memcache->set('online_members', $online_members);

        // 重新获取在线用户数据
        $online_members = $memcache->get('online_members');

        // 统计人数
        $online_members = count($online_members);

        // 返回数据
        return success($online_members);

    }

    // 2、缓存网站里面的局部数据，例如商城的商品分类，这种很少会变的数据

    // 3、访问频率限制功能【使用最多】
    // 例如:同一个手机号码一分钟之内只能发送1次短信验证，一天只能只能拥有3次发送短信的机会(缓存到今晚的23:59:59)。
    public function sendPhoneCode(Request $request)
    {
        $memcache = new Memcache();
        
        // 获取手机号码
        $phone = $request->params['phone'];

        // 是否获取过短信
        $code_key  = 'verify_code:' . $phone . ':code';
        $count_key = 'verify_code:' . $phone . ':count';
        $time_key  = 'verify_code:' . $phone . ':time';
        $count     = $memcache->get($count_key);

        // 是否获取过短信
        if ($count) {

            // 是否获取了三次短信
            if ($count > 2) {
                return fail('一天只能获取三次短信');
            }

            // 发送短信的时间
            $time = $memcache->get($time_key);
            if ($time) {
                return fail('一分钟内不能再次获取短信');
            }
        }

        // 获取六位验证码
        $code = get_random_number();

        // 发送验证码短信
        // $send_result = sendPhoneCode($phone, $code);
        // if(!$send_result) return fail('发送失败');

        // 缓存数据
        $code_result  = $memcache->set($code_key, $code, 300);
        $time_result  = $memcache->set($time_key, time(), 60);
        $count_result = $memcache->set($count_key, ++$count, over_time());
        if (!$code_result || !$time_result || !$count_result) {
            throw new Error('发送失败');
        }

        return success($code);
    }

    // 4、文章浏览数统计
    // 文章id作为key，有人点击页面时，使用memcache的incr指令增加浏览量
    // 定时任务：3点，没什么人的时候将memcache数据更新到mysql数据里面

    // 5、热门软件下载排行榜
    public function top()
    {
        // 获取数据 获取 数据一般可以缓存一天
        $key = 'soft_top';
        // 注意：一般来说我们设计的key可以都要进行md5处理，
        // 主要是为了防止key里存在特殊的字符或者是中文或者超过250字节【memcache默认key长度250】
        $key = md5($key);
        // halt($key);

        // 缓存多久 今天的 23:59:59 秒即可，就是当天的末点到现在的时间戳之间的秒数
        $tiem = strtotime(date('Ymd 23:59:59')) - time();

        // 方式一：查询缓存
        // $data = ProductCategory::cache(true)->order('sort', 'desc')->select();
        // 方式二：设置key
        // $data = ProductCategory::cache($key)->order('sort', 'desc')->select();
        // 方式三：设置缓存有效期
        // $data = ProductCategory::cache($key, $tiem)->order('sort', 'desc')->select();
        // 方式四：设置缓存的介质，本来是在cache($key, $time, "redis")第三个参数设置缓存类型的，但是TP6不知道怎么换
        // 只能在cache.php配置文件改默认缓存类型了
        $data = ProductCategory::cache($key, $tiem)->order('sort', 'desc')->select();
        // $data = Cache::get($key);

        return success($data);
    }
}
