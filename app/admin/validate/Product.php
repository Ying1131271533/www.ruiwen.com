<?php

namespace app\admin\validate;

use app\common\validate\BaseValidate;
use PDO;

class Product extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'id|商品id'                    => 'require|number|gt:0',
        'product_category_id|商品分类id' => 'require|number',
        'name|商品名称'                  => 'require|unique:product|max:50',
        'image|商品主图'                 => 'require',
        'slide_img|商品轮播图'            => 'require',
        'market_price|市场价'           => 'require|float',
        'shop_price|实际价格'            => 'require|float',
        'stock|库存'                   => 'require|number',
        'is_hot|是否热卖'                => 'require|in:0,1',
        'spec_type|规格类型'             => 'require|in:0,1',
        'specs|规格'                   => 'require|checkSpecs',
        'imgs|图册'                    => 'require|checkImgs',
    ];

    // 验证消息
    protected $message = [
        'name.unique'    => '商品名称已存在',
        'image.fileSize' => '商品主图内存不能超过2M',
    ];

    // 验证场景
    protected $scene = [
        'save'   => [
            'product_category_id',
            'name',
            'image',
            'slide_img',
            'market_price',
            'shop_price',
            'stock',
            'is_hot',
            'spec_type',
            'specs',
            'imgs',
        ],
        'delete' => ['id'],
        'update' => [
            'id',
            'product_category_id',
            'name',
            'image',
            'slide_img',
            'market_price',
            'shop_price',
            'stock',
            'is_hot',
            'spec_type',
            'specs',
            'imgs',
        ],
        'read'   => ['id'],
    ];

    // 规格规则
    protected $specsRule = [
        'specs_value_id|规格值id' => 'require|isCommaString',
        'price|规格价格'           => 'require|float',
        'stock|规格库存'           => 'require|number',
    ];

    // 规格规则信息
    protected $specsMessage = [
        'specs_value_id.require' => '规格值id不能为空',
        'specs_value_id.number'  => '规格值id必须为数字',
        'specs_value_id.float'   => '规格值id必须为浮点型',
    ];

    // 检查规格
    protected function checkSpecs($value, $rule = '', $data = [], $field = '', $field_msg = '')
    {
        if (!isset($value) && !is_array($value)) {
            return $field . '规格类型错误';
        }

        $validate = new Product;
        // 这里的话，checkSpesc必须要放在最后一个
        $validate->message = $this->specsMessage;
        foreach ($value as $item) {
            if (!is_array($item)) {
                return $field . '规格数据错误';
            }
            $result = $validate->check($item, $this->specsRule);
            if (!$result) {
                throw new \Exception($validate->getError());
            }
        }

        return true;
    }

    // 检查图册
    protected function checkImgs($value, $rule = '', $data = [], $field = '', $field_msg = '')
    {
        if (!is_array($value)) {
            return $field . '必需为数组';
        }

        foreach ($value as $img) {
            if (!$img) throw new \Exception('图册数组有空值');
        }
        return true;
    }
}
