<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;

class ProductCategory extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|分类id'   => 'require|number',
        'pid|父级id'  => 'require|number',
        'name|分类名称' => 'require|unique:product_category|max:20',
        // 'pic|分类图标'  => 'image|fileSize:2000000|fileExt:jpg,png,bmp,jpeg,gif',
        'sort|排序'   => 'lt:30000',
    ];

    // 验证消息
    protected $message = [
        'name.unique' => '分类名称已存在',
        'pic.fileSize' => '分类图标内存不能超过2M',
    ];

    // 验证场景
    protected $scene = [
        'save'   => ['pid', 'name', 'pic', 'sort'],
        'delete' => ['id'],
        'update' => ['id', 'pid', 'name', 'pic', 'sort'],
        'read'   => ['id'],
    ];
}
