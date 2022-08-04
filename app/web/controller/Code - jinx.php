<?php
declare (strict_types = 1);
namespace app\api\controller;

use phpmailer\PHPMailer;
use submall\MESSAGEXsend;
use think\facade\Cache;

// use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;

class Code extends Base
{
    /**
     * 获取验证码
     *
     * @return json     api返回的json数据
     */
    public function get_code()
    {
        $username      = $this->params['username'];
        $exist         = $this->params['is_exist'];
        $username_type = $this->check_username($username);
        $this->get_code_by_username($username, $username_type, $exist);
    }

    /**
     * 通过手机号/邮箱获取验证码
     *
     * @param  string    $username      手机号/邮箱
     * @param  string    $type          用户名类型手机号/邮箱
     * @param  int       $exist         手机号/邮箱是否应该存在于数据库中：0 否 1 是
     * @return json                     发送验证码
     */
    public function get_code_by_username($username, $type, $exist)
    {
        /*****************   用户名类型名称   *****************/
        if ($type == 'phone') {
            $type_name = '手机';
        } else {
            $type_name = '邮箱';
        }

        /*****************   检测手机号/邮箱是否存在   *****************/
        $this->check_exist($username, $type, $exist);

        /*****************   获取缓存类型   *****************/
        $cache = Cache::store('code');

        /*****************   检测验证码请求频率 60秒一次   *****************/
        if ($cache->get($username . '_last_send_time')) {
            if (time() - $cache->get($username . '_last_send_time') < 60) {
                $this->return_msg(400, $type_name . '验证码，每60秒只能发送一次！');
            }
        }

        /*****************   生产验证码   *****************/
        $code = $this->make_code(6);

        /*****************   存储验证码，方便比对，md5加密   *****************/
        $md5_code = md5($username) . '_' . md5($code);
        $cache->set($username . '_code', $md5_code);

        /*****************   存储验证码的发送时间   *****************/
        $cache->set($username . '_last_send_time', time());

        /*****************   发送验证码   *****************/
        if ($type == 'phone') {
            $this->send_code_to_phone($username, $code);
        } else {
            $this->send_code_to_email($username, $code);
        }
    }

    /**
     * 生成验证码
     *
     * @param  int    $number     验证码位数
     * @return int                生产的验证码
     */
    public function make_code($number = 6)
    {
        $max = pow(10, $number) - 1;
        $min = pow(10, $number - 1);
        return (string) rand($min, $max);
    }

    /**
     * 向手机发送验证码(使用SDK)
     *
     * @param  string    $phone    目标的手机号
     * @param  int       $code     验证码
     * @return json                返回发送手机验证码的提示信息
     */
    public function send_code_to_phone($phone, $code)
    {
        $config  = config('sms');
        $submail = new MESSAGEXsend($config);

        $submail->SetTo($phone);
        $submail->SetProject($config['project_id']);
        $submail->AddVar('code', $code);
        $submail->AddVar('time', '五分钟');
        $xsend = $summail->xsend();
        if ($xsend != 'success') {
            $this->return_msg(400, $xsend['msg']);
        } else {
            $this->return_msg(200, '手机验证码已发送，每天只能发送5次，请在五分钟内验证！');
        }
    }

    /**
     * 向邮箱发送验证码
     *
     * @param  string    $email    目标的邮箱
     * @param  int       $code     验证码
     * @return json                返回发送邮箱验证码的提示信息
     */
    public function send_code_to_email($email, $code)
    {
        $toemail = $email;
        // 这个PHPMailer 就是之前从 Github上下载下来的那个项目
        // require 'phpmailer\PHPMailer';
        $mail = new PHPMailer;
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式，
        // 可选择的值有 1 、 2 、 3
        // $mail->SMTPDebug = 2;
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
        // qq 邮箱的 smtp服务器地址，这里当然也可以写其他的 smtp服务器地址
        $mail->Host = 'smtp.qq.com';
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = '1131271533@qq.com';
        // 这个就是之前得到的授权码，一共16位
        $mail->Password = 'qlegosubdioxbaeb';
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        // //设置ssl连接smtp服务器的远程服务器端口号，可选465或587
        $mail->Port = 465;
        //设置smtp的helo消息头 这个可有可无 内容任意
        // $mail->Helo = 'Hello smtp.qq.com Server';
        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        // $mail->Hostname = 'http://www.lsgogroup.com';
        //设置发送的邮件的编码 也可选 GB2312
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('1131271533@qq.com', '神织知更');
        // $to 为收件人的邮箱地址，如果想一次性发送向多个邮箱地址，则只需要将下面这个方法多次调用即可
        $mail->addAddress($toemail);
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        // 该邮件的主题
        $mail->Subject = '【风信子】 您有新的验证码！';
        // 该邮件的正文内容
        $mail->Body = '您的验证码为： <b>' . $code . '</b>';
        //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
        $mail->addAttachment('E:\樱\图片\心灵\小包.jpg', '小包.jpg');
        //同样该方法可以多次调用 上传多个附件
        // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
        // 使用 send() 方法发送邮件
        if (!$mail->send()) {
            $this->return_msg(400, $mail->ErrorInfo);
        } else {
            $this->return_msg(200, '验证码已成功发送！');
        }
    }
}
