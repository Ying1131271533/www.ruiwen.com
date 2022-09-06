<?php

namespace app\web\controller;

use app\Request;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearch
{
    // protected $https;
    protected $client;

    public function __construct()
    {
        // $this->https = config('elasticsearch.https');
        $this->client = ClientBuilder::create()->setHosts(config('elasticsearch.http'))->build();
        // 密钥保护
        // $this->client = ClientBuilder::create()->setApiKey('id', 'api_key')->build();
    }

    // 列表
    public function index()
    {
        // 设置查询的条件
        $params = [
            'index' => 'user',
            'id'    => 'id',
            'body'  => ['testField' => 'name'],
        ];
        // return json($params);
        $results = $this->client->search($params); // es搜索
        return success($results);
    }

    // 索引 创建
    public function save(Request $request)
    {
        // 接收参数
        $params = $request->param();
        return success($params);
        
        $results = $this->client->search($params); // es搜索
        return success($results);
    }
}
