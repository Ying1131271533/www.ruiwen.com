<?php

namespace app\common\controller;

use app\Request;
use think\facade\Db;

class Ajax
{
    /**
     * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
     * @author: 神织知更
     * @time: 2022/02/26 15:57
     *
     * 修改数据状态
     *
     * @param  Request      $request    请求对象
     * @return json                     api返回的json数据
     */
    public function changeStatus(Request $request)
    {
        $params = $request->params;
        $data = [
            'id'    => $params['id'],
            'value' => $params['value'] ? 0 : 1,
            'field' => $params['field'],
            'db'    => $params['db'],
            'url'   => $params['url'],
        ];
        
        $result = Db::table($data['db'])->where('id', $data['id'])->update($data['field'], $data['value']);
        if (empty($result)) {
            return ['code' => 1, 'msg' => '修改失败'];
        }
        $url = str_replace('value/' . $params['value'], 'value/' . $data['value'], $data['url']);
        
        return success(['value' => $data['value'], 'url' => $url]);
    }
}

/* 
    html使用
    <a onclick="ajax_status(this)" data-id="{$value.id}" data-url="{:url('ajax/status', ['id' => $value.id, 'value' => $value.ispos, 'field' => 'ispos', 'db' => 'article'])}" class="bth-a ajax-status {$value.ispos == 1 ?: 'error-c'}"></a>

    js使用
    function ajax_status(obj)
    {
        var id = $(obj).attr("data-id");
        var url = $(obj).attr("data-url");
        
        $.get(url, {id: id, url: url}, function(data){
        
            if(data.code == 0)
            {
                if(data.value == 1){
                    $(obj).removeClass("error-c").attr("data-url", data.url);
                }else{
                    $(obj).addClass("error-c").attr("data-url", data.url);
                }
            }else{
                return layer.alert(data.msg, {icon:2});
            }
            
        }, 'json');
    }
    
*/