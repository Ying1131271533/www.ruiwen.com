<?php

namespace app\web\controller;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearch
{
    public function index()
    {
        // 配置
        $https = [
            '127.0.0.1:9200',
            // 'user:password@127.0.0.1:9200',
        ];
        // 获取连接
        $client = ClientBuilder::create()->setHosts($https)->build();
        //设置查询的条件
        $params = [
            'index' => 'user',
            'id'    => 'id',
            'body'  => ['testField' => 'name'],
        ];
        // return json($params);
        $results = $client->search($params); //es搜索
        return success($results);
    }
}
