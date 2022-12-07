<?php

namespace app\common\logic\lib;

use Exception;
use think\facade\Db;

class Data
{
    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/12/06 16:05
     *
     * 修改数据字段的值
     *
     * @param  array    $data   数据
     * @return json             api返回的json数据
     */
    public function changeFieldValue($data)
    {
        $result = Db::table($data['db'])->where('id', $data['id'])->update([$data['field'] => $data['value']]);
        if (empty($result)) throw new Exception('更新失败', config('status.faild'));
        return [
            'id' => $data['id'],
            'value' => $data['value'],
        ];
    }
}
