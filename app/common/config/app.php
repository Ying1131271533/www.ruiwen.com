<?php

return [
    // 可以上传的文件类型和上传规则
    'upload_file_type' => [
        // 单张图片
        'image'  => ['image' => 'fileSize:2097152|fileExt:jpg,jpeg,gif,png,bmp'],
        // 多张图片
        'images' => ['image' => 'fileSize:2097152|fileExt:jpg,jpeg,gif,png,bmp'],
        // excel表格
        'excel'  => ['file' => 'fileSize:2097152|fileExt:xls,xlsx'],
        // word文档
        'word'   => ['file' => 'fileSize:2097152|fileExt:doc'],
    ],
];
