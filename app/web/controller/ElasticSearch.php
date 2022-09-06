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
        $this->client = ClientBuilder::create()->setHosts(config('app.elasticsearch.http'))->build();
        // 密钥保护
        // $this->client = ClientBuilder::create()->setApiKey('id', 'api_key')->build();
    }

    // 查询所有索引
    public function index_list()
    {
        $results = $this->client->indices();
        return success($results);
    }

    // 索引 创建
    public function index_save(Request $request)
    {
        // 接收参数
        $params = $request->params;
        return success($params);
        // 创建索引(表)
        $results = $this->client->index($params);
        return success($results);
    }
}