<?php

namespace app\common\validate;

use think\facade\Db;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 获取验证器定义的字段
     *
     * @param  array    $params        参数
     * @return array                   需要验证的参数名
     */
    public function getDateByRule(array $params)
    {
        $currentScene = $this->currentScene; // 当前场景
        $scene        = $this->scene; // 验证器的所有场景

        // 判断场景是否存在，检测参数中间已经做了，这里可以不要
        /* if (!array_key_exists($currentScene, $scene)) {
        throw new Fail(['msg' => '检验场景不存在']);
        } */

        // 获取场景需要检验的字段，过滤掉可能被恶意添加的字段，例如强加上去的用户余额字段 total
        $rules    = $scene[$currentScene];
        $newArray = [];
        foreach ($rules as $value) {
            // $newArray[$value] = $params[$value];
            $newArray[$value] = isset($params[$value]) ? $params[$value] : '';
        }
        return $newArray;
    }

    protected function canBeEmpty($value, $rule = '', $data = '', $field = '')
    {
        return true;
    }

    protected function isCommaString($value, $rule = '', $data = '', $field = '')
    {
        if (strpos($value, ',')) {
            $specs_valeus = explode(',', $value);
            foreach ($specs_valeus as $specs_value) {
                if (!is_numeric($specs_value) || $specs_value == 0) {
                    return $field . '格式错误';
                }
            }
        } elseif (!is_numeric($value) || $value == 0) {
            return $field . '格式错误';
        }
        return true;
    }

    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (!is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return $field . '必须是正整数';
    }

    // 该id是否存在于数据库
    protected function idIsExist($value, $rule = '', $data = '', $field = '')
    {
        $data = Db::table($rule)->find($value);
        if (!$data) {
            return '该'.$field.'的数据不存在';
        }

        return true;
    }

}
