<?php

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/10/18 14:40
 */

return [
    // 前缀 激活Token
    'avtive_pre'   => 'avtive_account_pre_',
    // 前缀 登录Token
    'token_pre'    => 'access_token_pre_',
    // 登录Token持续时间(一天)
    'token_expire' => 24 * 3600,
    // 前缀 登录验证码
    'code_pre'     => 'login_pre_',
    // 登录验证码过期时间
    'code_expire'  => 120,
    // 文件数据过期时间 15min
    'code_expire'  => 3600 / 4,
];
