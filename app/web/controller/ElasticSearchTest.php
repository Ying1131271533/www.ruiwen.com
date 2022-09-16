<?php

namespace app\web\controller;

use app\lib\exception\Fail;
use app\Request;
use Elasticsearch\ClientBuilder;

class ElasticSearchTest
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(config('app.elasticsearch.http'))->build();
    }

    // 索引 创建
    public function index_save(Request $request)
    {
        // 接收参数
        $params = $request->params;

        // 数据设置
        $params = [
            'index' => $params['index'],
            'body'  => [
                'settings' => [
                    'number_of_shards'   => 3, // 主分片数
                    'number_of_replicas' => 1, // 主分片的副本数
                ],
                'mappings' => [ // 映射
                    'properties' => [
                        'title'    => [
                            'type' => 'text',
                        ],
                        'category' => [
                            'type' => 'keyword',
                            'fielddata' => true,
                        ],
                        'price'    => [
                            'type' => 'double',
                        ],
                        'images'   => [
                            'type'  => 'keyword',
                            'index' => false,
                        ],
                    ],
                ],
            ],
        ];

        // 保存
        try {
            $result = $this->client->index($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 查询 索引
    public function index_read(Request $request)
    {
        $index  = $request->params['index'];
        $params = [
            'index'  => [$index],
            'client' => [
                'ignore' => [404, 400],
            ],
        ];

        try {
            $alias    = $this->client->indices()->getAlias($params);
            $mapping  = $this->client->indices()->getMapping($params);
            $settings = $this->client->indices()->getSettings($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        $result = [
            'settings' => $settings,
            'mapping'  => $mapping,
            'alias'    => $alias,
        ];
        return success($result);
    }

    // 索引 删除
    public function index_delete(Request $request)
    {
        $params = [
            'index' => $request->params['index'],
        ];
        try {
            $result = $this->client->indices()->delete($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }
        return success($result);
    }

    // 数据 保存
    public function save(Request $request)
    {
        // 接收参数
        // $params = $request->params;

        $params = [
            'id'       => $request->param('id/d'),
            'title'    => $request->param('title/s'),
            'category' => $request->param('category/s'),
            'price'    => $request->param('price/f'),
            'images'   => $request->param('images/s'),
        ];

        // 组装数据
        $data = [
            'index' => 'product',
            'id'    => $params['id'], // 这里的id相当于主键，所以body可以不添加id字段
            'body'  => $params,
        ];

        // 保存
        try {
            $result = $this->client->index($data);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 读取
    public function read(int $id)
    {
        // 组装数据
        $params = [
            'index' => 'product',
            'id'    => $id,
        ];

        // 保存
        try {
            $result = $this->client->get($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 更新
    public function update(Request $request)
    {
        // 接收参数
        // $params = $request->params;
        $params = [
            'id'       => $request->param('id/d'),
            'title'    => $request->param('title/s'),
            'category' => $request->param('category/s'),
            'price'    => $request->param('price/f'),
            'images'   => $request->param('images/s'),
        ];

        // 组装数据
        $data = [
            'index' => 'product',
            'id'    => $params['id'],
            'body'  => [
                'doc' => $params, // 必须带上这个，表示是文档操作
            ],
        ];

        // 更新
        try {
            // 这个好像是局部更新
            $result = $this->client->update($data);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 删除
    public function delete(int $id)
    {
        // 组装数据
        $params = [
            'index' => 'product',
            'id'    => $id,
        ];

        // 删除
        try {
            $result = $this->client->delete($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 批量保存
    public function bulk_save(Request $request)
    {
        // 接收参数
        $params = [
            'id'       => $request->param('id/d'),
            'title'    => $request->param('title/s'),
            'category' => $request->param('category/s'),
            'price'    => $request->param('price/f'),
            'images'   => $request->param('images/s'),
        ];

        // 处理批量数据
        $data = [];
        for ($i = 1; $i <= 10; $i++) {
            $data['body'][] = [
                'index' => [
                    '_index' => 'product',
                    '_id'    => $i,
                ],
            ];
            $data['body'][] = [
                'id'       => $i,
                'title'    => "[$i]" . $params['title'],
                'category' => $params['category'],
                'price'    => $params['price'] + $i,
                'images'   => $params['images'],
            ];
        }

        // 批量保存
        try {
            $result = $this->client->bulk($data);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 批量更新
    public function bulk_update(Request $request)
    {
        $params = [];
        for ($i = 1; $i <= 10; $i++) {
            $params['body'][] = [
                'update' => [
                    '_index' => 'product',
                    '_id'    => $i,
                ],
            ];
            $params['body'][] = [
                'doc' => [
                    'username' => '角色' . $i,
                ],
            ];
        }

        // 批量更新
        try {
            $result = $this->client->bulk($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }
        return success($result);
    }

    // 数据 批量删除
    public function bulk_delete(Request $request)
    {
        // 处理批量数据
        $params = [];
        for ($i = 1; $i <= 10; $i++) {
            $params['body'][] = [
                'delete' => [
                    '_index' => 'product',
                    '_id'    => $i,
                ],
            ];
        }

        // 批量删除
        try {
            $result = $this->client->bulk($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }
        return success($result);
    }

    // 查询
    public function search(Request $request)
    {
        // 分页数据
        $page = $request->page;
        $size = $request->size;
        // 全部
        // $params = [
        //     'index' => 'product',
        //     // (当前页码 - 1) * 每页条数
        //     'from'  => ($page - 1) * $size,
        //     'size'  => $size,
        // ];

        // 条件 分页
        $params = [
            'index' => 'product',
            'body'  => [
                'query' => [
                    'match' => [
                        'title' => '苹果',
                    ],
                ],
            ],
            // (当前页码 - 1) * 每页条数
            'from'  => ($page - 1) * $size,
            'size'  => $size,
        ];

        // 查询
        try {
            $result = $this->client->search($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        // 返回结果
        return success($result);
    }
}
