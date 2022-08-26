<?php
namespace app\web\logic;

use app\common\model\User as ModelUser;
use app\lib\exception\Fail;
// use app\web\logic\WechatApplet; // 这里使用了下面的工厂模式，所以不要了
use app\lib\login\ClassAttr;

class Login extends Token
{
    public static function loginAccount($user_id)
    {
        $token = self::getToken();
        self::saveToCache($token, ['user_id' => $user_id]);

        // 保存登录信息
        $updateData = [
            'id'        => $user_id,
            'last_ip'   => get_client_ip(),
            'last_time' => time(),
        ];
        ModelUser::update($updateData);

        return $token;
    }

    /**
     * 微信小程序通过code换取openid
     *
     * @param  string   $code   code
     * @return string           返回token
     */
    public function loginWechatApplet($code)
    {
        $login = (new ClassAttr())->initClass('wx', ['code' => $code]);
        if (!$login) {
            throw new Fail('工厂微信登录类不存在');
        }

        $wxReuslt = $login->get();
        // $wxReuslt = (new WechatApplet($code))->get();
        $token = self::getToken();

        $data = [
            'openid'    => $wxReuslt['openid'],
            'last_ip'   => get_client_ip(),
            'last_time' => time(),
        ];

        $user = ModelUser::where('openid', $wxReuslt['openid'])->find();
        if (!$user) {
            $user = ModelUser::create($data);
            if (!$user) {
                throw new Fail('登录出错');
            }

        }

        $wxReuslt['user_id'] = $user['id'];
        self::saveToCache($token, $wxReuslt);
        return $token;
    }
}
