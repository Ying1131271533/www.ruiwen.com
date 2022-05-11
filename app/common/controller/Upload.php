<?php
namespace app\common\controller;

use app\lib\exception\Fail;
use app\Request;
use think\facade\Filesystem;

class Upload
{
    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/04/23 10:26
     *
     * 上传文件
     *
     * @param  Request          $request    请求对象
     * @return string|array     $savename   返回文件路径
     */
    public function file(Request $request)
    {
        // 获取上传文件类型
        $file_type = $request->params['type'];

        // 获取上传文件和判断文件是否为空
        $file = $request->file();
        if (empty($file) || empty($file[$file_type])) {
            throw new Fail('上传的文件不能为空');
        }

        // 获取上传文件类型的配置
        $config = config('app.upload_file_type');
        $path = $this->upload($file, $config[$file_type], $file_type);
        return success(['path' => $path]);
    }

    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/04/23 10:26
     *
     * 上传文件方法
     *
     * @param  array        $file           文件数据
     * @param  array        $validate       验证规则
     * @param  string       $file_type      文件类型
     * @return string|array $savename       返回文件路径
     */
    protected function upload($file, $validate, $file_type)
    {
        try {
            // 验证文件
            validate($validate)->check($file);

            // 获取需要上传的相关类型文件的数据
            $file = $file[$file_type];

            // 上传文件
            if (is_array($file)) { // 上传多个文件
                $savename = [];
                foreach ($file as $value) {
                    $path       = Filesystem::disk('storage')->putFile($file_type, $value);
                    $path       = str_replace('\\', '/', '/storage/' . $path);
                    $savename[] = $path;
                }
            } else { // 上传单个文件
                $path     = Filesystem::disk('storage')->putFile($file_type, $file);
                $savename = str_replace('\\', '/', '/storage/' . $path);
            }
            // 返回文件路径
            return $savename;
        } catch (\think\exception\ValidateException $e) {
            throw new Fail($e->getMessage());
        }
    }
}
