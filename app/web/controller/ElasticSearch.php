<?php

namespace app\web\controller;

use app\lib\exception\Fail;
use app\Request;
use Elasticsearch\ClientBuilder;

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

    // 索引 创建
    public function index_save(Request $request)
    {
        // 接收参数
        $params = $request->params;

        // 普通创建（不指定类型，ES会自动匹配类型）
        // $data = [
        //     'index' => $data['index']
        // ];

        // 连同设置，字段类型一起创建
        /* $data = [
        'index' => 'user',
        'body'  => [
        'settings' => [
        'number_of_shards'   => 3, // 分片数
        'number_of_replicas' => 1, // 副本数
        ],
        // 定义字段类型
        'mappings' => [
        '_source'    => [
        'enabled' => true,
        ],
        'properties' => [
        'name' => [
        'type' => 'keyword',
        ]
        ],
        ],
        ],
        ]; */

        // 数据设置
        $params = [
            'index' => $params['index'],
            'body'  => [
                // 我们将分配3个主分片和一份副本
                // 'number_of_shards' => 3 需要开启单点集群
                // 'number_of_replicas' => 2 需要开启多个节点的集群
                // 'settings'=>[
                //     'number_of_shards' => 3, // 主分片数
                //     'number_of_replicas' => 1 // 主分片的副本数
                // ],
                'mappings' => [ // 映射
                    '_source'    => [ // 存储原始文档
                        'enabled' => 'true',
                    ],
                    'properties' => [
                        'name' => [
                            'type'  => 'text',
                            'index' => true,
                        ],
                        'age'  => [
                            'type'  => 'integer',
                            'index' => true,
                        ],
                        'sex'  => [
                            'type'  => 'keyword',
                            'index' => true,
                        ],
                    ],
                ]
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

    // 查询所有索引
    public function index_list()
    {
        $params = [
            'index'  => ['user', 'shopping'],
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
        $params = $request->params;

        // 组装数据
        $data = [
            'index' => 'user',
            'id'    => 1001, // 这里的id相当于主键，所以body就不要添加id字段
            'body'  => [
                'username' => '神织恋',
                'age'      => 17,
                'sex'      => '女',
            ],
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
            'index' => 'user',
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
        $params = $request->params;

        // 组装数据
        $data = [
            'index' => 'user',
            'id'    => $params['id'],
            'body'  => [
                'doc' => [ // 必须带上这个，表示是文档操作
                    'username' => $params['username'],
                    'age'      => $params['age'],
                    'sex'      => $params['sex'],
                ],
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
            'index' => 'user',
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
        // 处理批量数据
        $params = [];
        for ($i = 1; $i < 10; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'user',
                    '_id'    => $i,
                ],
            ];
            $params['body'][] = [
                'username' => '人物' . $i,
                'age'      => 20 + $i,
                'sex'      => $i % 2 ? '男' : '女',
            ];
        }

        // 批量保存
        try {
            $result = $this->client->bulk($params);
        } catch (\Throwable $th) {
            throw new Fail($th->getMessage());
        }

        return success($result);
    }

    // 数据 批量更新
    public function bulk_update(Request $request)
    {
        $params = [];
        for ($i = 1; $i < 10; $i++) {
            $params['body'][] = [
                'update' => [
                    '_index' => 'user',
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
        for ($i = 1; $i < 10; $i++) {
            $params['body'][] = [
                'delete' => [
                    '_index' => 'user',
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
        // 全部 排序 字段排除
        $params = [
            'index' => 'user',
            'body'  => [
                '_source' => ['username', 'age'],
                'sort'    => [
                    'age' => 'asc',
                ],
            ],
        ];

        // 分页数据
        $page = $request->page;
        $size = $request->size;

        // 条件 分页
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query' => [
        //             'match' => [
        //                 'sex' => '女',
        //             ],
        //         ],
        //     ],
        //     // (当前页码 - 1) * 每页条数
        //     'from'  => ($page - 1) * $size,
        //     'size'  => $size,
        // ];

        // 并且 数组形式
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query' => [
        //             'bool' => [
        //                 'must' => [
        //                     [
        //                         'match' => [
        //                             'age' => 26
        //                         ]
        //                     ],
        //                     // 等于下面的must_ont
        //                     // [
        //                     //     'match' => [
        //                     //         'sex' => "男"
        //                     //     ]
        //                     // ],
        //                 ],
        //                 'must_not' => [
        //                     [
        //                         'match' => [
        //                             'sex' => '女'
        //                         ]
        //                     ]
        //                 ]
        //             ],
        //         ],
        //     ],
        // ];

        // 并且 json形式
        // $params = [
        //     'index' => 'user',
        //     'body'  => '{
        //         "query": {
        //             "bool": {
        //                 "must": [
        //                     {
        //                         "match": { "age": 25 }
        //                     },
        //                     {
        //                         "match": { "sex": "男" }
        //                     }
        //                 ]
        //             }
        //         }
        //     }',
        // ];

        // 或者
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query' => [
        //             'bool' => [
        //                 'should' => [
        //                     [
        //                         'match' => [
        //                             'username' => '神织恋',
        //                         ],
        //                     ],
        //                     [
        //                         'match' => [
        //                             'age' => 25
        //                         ]
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]
        // ];

        // 范围
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query' => [
        //             'bool' => [
        //                 'filter' => [
        //                     [
        //                         'range' => [
        //                             'age' => [
        //                                 'gte' => 25,
        //                             ]
        //                         ]
        //                     ],
        //                     [
        //                         'range' => [
        //                             'age' => [
        //                                 'lte' => 28
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //             ],
        //         ],
        //     ],
        // ];

        // 模糊查询
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query' => [
        //             // 模糊
        //             // 中文和英文的分词方式不一样
        //             'fuzzy' => [
        //                 // 英文
        //                 // 'username' => [
        //                 //     'value'     => 'jin',
        //                 //     // 有效的偏移距离为 0, 1, 2 默认自动auto
        //                 //     // 0 只能匹配到 jin
        //                 //     // 1 可以匹配到 jinx
        //                 //     // 2 可以匹配到 role-6jinx-ying
        //                 //     // auto 可以匹配到 jinx 不过会随着value不同而不同
        //                 //     'fuzziness' => 'auto'
        //                 // ],
        //                 // 中文
        //                 // 'username' => [
        //                 //     'value'     => '神',
        //                 //     // 有效的偏移距离为 0, 1, 2 默认自动auto
        //                 //     // 0 可以匹配到 神织恋 神织知更
        //                 //     // 1 可以匹配到 所有包含中文的数据
        //                 //     // 2 可以匹配到 所有包含中文的数据
        //                 //     // auto 可以匹配到 神织恋 神织知更
        //                 //     'fuzziness' => 0
        //                 // ],
        //                 // 中文 2个
        //                 // 'username' => [
        //                 //     'value'     => '神织',
        //                 //     // 有效的偏移距离为 0, 1, 2 默认自动auto
        //                 //     // 0 无匹配
        //                 //     // 1 可以匹配到 神织恋 神织知更
        //                 //     // 2 可以匹配到 所有包含中文的数据
        //                 //     // auto 无匹配
        //                 //     'fuzziness' => 2
        //                 // ],
        //                 // 中文 3个
        //                 'username' => [
        //                     'value'     => '神织恋',
        //                     // 有效的偏移距离为 0, 1, 2 默认自动auto
        //                     // 0 无匹配
        //                     // 1 无匹配
        //                     // 2 可以匹配到 神织恋 神织知更
        //                     // auto 无匹配
        //                     'fuzziness' => 2
        //                 ],
        //             ],
        //             // 查询支持开箱即用的模糊匹配
        //             // 'match' => [
        //             //     'username' => [
        //             //         'query'     => 'jinx',
        //             //         'fuzziness' => 'auto',
        //             //         'operator'  => 'and',
        //             //     ],
        //             // ],
        //             // 多个模糊
        //             // "multi_match" => [
        //             //     "fields" =>  [ "text", "title" ],
        //             //     "query" =>     "SURPRIZE ME!",
        //             //     "fuzziness" => "AUTO"
        //             // ]
        //         ],
        //     ],
        // ];

        // 高亮
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'query'     => [
        //             'fuzzy' => [
        //                 'username' => [
        //                     'value'     => '神织恋',
        //                     // 有效的偏移距离为 0, 1, 2 默认自动auto
        //                     // 0 无匹配
        //                     // 1 无匹配
        //                     // 2 可以匹配到 神织恋 神织知更
        //                     // auto 无匹配
        //                     'fuzziness' => 2,
        //                 ],
        //             ],
        //         ],
        //         'highlight' => [
        //             'pre_tags'  => ['<font color="red">'],
        //             'post_tags' => ['</font>'],
        //             'fields'    => [
        //                 'username' => (object) [],
        //             ],

        //         ],
        //     ],
        // ];

        // 聚合查询
        // $params = [
        //     'index' => 'user',
        //     'body'  => [
        //         'aggs' => [
        //             // 统计相同数据有几个进行分组 好像不能使用中文数据进行统计
        //             'age_group' => [
        //                 'terms' => [
        //                     'field' => 'age'
        //                 ]
        //             ],
        //             // 平均值名称
        //             // 'age_avg' => [
        //             //     'avg' => [
        //             //         'field' => 'age',
        //             //     ]
        //             // ],
        //             // 'age_max' => [
        //             //     'max' => [
        //             //         'field' => 'age',
        //             //     ]
        //             // ]
        //         ],
        //     ],
        //     // 只显示统计结果，不显示数据
        //     'size' => 0
        // ];

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
