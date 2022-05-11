<?php
namespace lib;

use app\lib\exception\Fail;
use phpseclib\Crypt\RSA;
use think\facade\Cache;

class Crypt
{
    /**
     * 获取密钥
     *
     * @param  int    $expire_time      过期时间
     * @return Response                 api返回的json数据
     */
    public static function key(int $expire_time = 14400)
    {
        // 获取密钥
        $rsa = new RSA();
        extract($rsa->createKey(2048));
        // 缓存公钥和私钥
        $result = Cache::store('redis')->set($publickey, $privatekey, $expire_time);
        if (empty($result)) {return false;}
        return ['publickey' => $publickey, 'privatekey' => $privatekey];
    }
    /**
     * 获取服务端公钥和用户端私钥
     *
     * @return Response    api返回的json数据
     */
    public static function web_key()
    {
        // 获取密钥
        $rsa = new RSA();
        extract($rsa->createKey(2048));
        // 缓存公钥和私钥
        $result = Cache::store('redis')->set($publickey, $privatekey, 60);
        if (empty($result)) {return false;}
        
        // 获取用户端密钥
        // $web_rsa = new RSA();
        $web_key = $rsa->createKey(2048);
        // 缓存公钥和私钥
        $web_result = Cache::store('redis')->set($web_key['privatekey'], $web_key['publickey'], 60);
        if (empty($web_result)) {return false;}

        return [
            'publickey'      => $publickey,
            'web_privatekey' => $web_key['privatekey'],
        ];
    }

    /**
     * 获取密钥和签名
     *
     * @param  string    $sign_str      需要签名的字符串
     * @param  string    $publickey     公钥
     * @return string    $sign          返回签名
     */
    public static function sign(string $sign_str, string $publickey)
    {
        $rsa = new RSA();
        $rsa->loadKey($publickey);
        $rsa->setEncryptionMode(RSA::ENCRYPTION_NONE);
        $sign = $rsa->sign($sign_str);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * rsa 加密数据
     * 如果密钥是2048位的只能加密256个字符, 1024位的密钥则是128(117)个字符
     *
     * @param  string   $sign           签名
     * @param  string   $publickey      通过公钥找到缓存的私钥
     * @return boolean                  布尔值
     */
    public static function verify(string $sign_str, string $sign, string $publickey)
    {
        // 获取私钥
        $privatekey = Cache::store('redis')->get($publickey);
        if (empty($privatekey)) {return false;}

        // RSA
        $rsa = new RSA();
        $rsa->loadKey($privatekey);
        // 加密模式
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        // 验签
        $verfiyResult = $rsa->verify($sign_str, base64_decode($sign));
        if (empty($verfiyResult)) {return false;}

        return true;
    }

    /**
     * rsa 加密数据
     * 如果密钥是2048位的只能加密256个字符, 1024位的密钥则是128(117)个字符
     *
     * @param  array    $data           需要加密的数组
     * @param  string   $publickey      通过私钥找到公钥
     * @return array                    加密数据
     */
    public static function encrypt(array $data, string $privatekey)
    {
        // 获取公钥
        $publickey = Cache::store('redis')->get($privatekey);
        if(empty($publickey)) throw new Fail('请求超时或公钥有误！');

        $rsa = new RSA();
        $rsa->loadKey($publickey);
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        foreach ($data as $key => $value) {
            $data[$key] = base64_encode($rsa->encrypt($value));
        }

        return $data;
    }

    /**
     * rsa 解密数据
     * 如果密钥是2048位的只能解密256个字符, 1024位的密钥则是128(117)个字符
     *
     * @param  array    $data           需要解密的数组
     * @param  string   $publickey      公钥
     * @return array                    解密数据
     */
    public static function decrypt(array $params)
    {
        $publickey      = $params['publickey'];
        $web_privatekey = $params['web_privatekey'];
        unset($params['publickey']);
        unset($params['web_privatekey']);
        if (!empty($params['sign'])) {
            unset($params['sign']);
        }

        // 获取私钥
        $privatekey = Cache::store('redis')->get($publickey);
        if (empty($privatekey)) throw new Fail('私钥获取失败');

        // RSA
        $rsa = new RSA();
        $rsa->loadKey($privatekey);
        // 加密模式
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);

        // 参数解密
        $data = [];
        foreach ($params as $key => $value) {
            // 解密
            $value = $rsa->decrypt(base64_decode($value));
            if (empty($value)) throw new Fail('解密失败');
            $data[$key] = $value;
        }

        // 成功获取解密数据后，删除私钥，防止二次使用
        Cache::store('redis')->delete($publickey);

        // 返回解密数据
        return $data;
    }

}
