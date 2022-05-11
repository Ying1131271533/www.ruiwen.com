<?php
namespace lib;

use Firebase\JWT\JWT;

class Jwttoken
{
    // 生成token
    public static function createJwt(array $data, string $key, int $expire_time = 14400)
    {
        // 加密数据
        $tokenData = array(
            "iss"  => "www.jinx.com", // 签发组织
            "aud"  => "api.jinx.com", // 签发作者
            "exp"  => time() + $expire_time, // 过期时间
            "data" => $data, // 数据
        );
        $token = JWT::encode($tokenData, $key, 'HS256');
        return $token;
    }

    // 校验jwt权限API
    public static function verifyJwt(string $token, string $key)
    {
        try {
            $jwtAuth  = json_encode(JWT::decode($token, $key, array('HS256')));
            $authInfo = json_decode($jwtAuth, true);
            $msg      = [];
            if (!empty($authInfo['data'])) {
                $msg = [
                    'code' => 0,
                    'msg'  => 'Token验证通过',
                    'data' => $authInfo['data'],
                ];
            } else {
                //Token验证不通过,用户不存在
                $msg = [
                    'code' => 10001,
                    'msg'  => '当前用户不存在',
                ];
            }
            return $msg;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return [
                'code' => 10002,
                'msg'  => 'Token无效',
            ];
            exit;
        } catch (\Firebase\JWT\ExpiredException $e) {
            //Token过期
            return [
                'code' => 10003,
                'msg'  => '登录信息已超时，请重新登录',
            ];
            exit;
        } catch (Exception $e) {
            return [
                'code' => 10004,
                'msg'  => '未知错误',
            ];
            exit;
        }
    }
}
