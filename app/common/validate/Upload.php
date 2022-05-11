<?php

namespace app\common\validate;

class Upload extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'type' => 'require|uploadFileType',
    ];

    // 验证消息
    protected $message = [
        'type.require' => '文件类型不能为空',
    ];

    // 验证场景
    protected $scene = [
        'file'  => ['type'],
    ];
    
    // 可以上传的文件类型
    protected function uploadFileType($value, $rule = '', $data = '', $field = '')
    {
        // 获取上传文件类型的配置
        $file_type = config('app.upload_file_type');
        if (!array_key_exists($value, $file_type)) {
            return '文件类型：“' . $value . '”没有可以上传的方式';
        }
        return true;
    }
}
