<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/12
 * Time: 9:26
 */

require "../vendor/autoload.php";

$client = Elasticsearch\ClientBuilder::create()->setHosts(array('129.204.182.233:9200'))->build();

// 创建帖子索引
$params = [
  'index' => 'post',
  'body'=>[
      'settings'=>[
          'number_of_shards'=>2,
          'number_of_replicas'=>0
      ],
      "mappings"=>[
          "default"=>[
              "properties"=>[
                  "userId"=> [
                      "type" => "integer"
                  ],
                  "content"=>[
                      "type" => "text",
                      "analyzer"=>"standard"
                  ],
                  "anonymous"=>[
                      "type"=>"boolean"
                  ],
                  "typeId"=>[
                      "type"=>"integer"
                  ],
                  "parise"=>[
                      "type"=>"integer"
                  ],
                  "status"=>[
                      "type"=>"boolean"
                  ],
                  "create_time"=>[
                      "type"=> "date",
                      "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                  ],
                  "update_time"=>[
                      "type"=> "date",
                      "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                  ]
              ]
          ]
      ]
  ]
];
$response = $client->indices()->create($params);
echo "创建帖子索引成功:".$response."\r\n";

// 创建评论索引
$params = [
    'index' => 'reply',
    'body'=>[
        'settings'=>[
            'number_of_shards'=>2,
            'number_of_replicas'=>0
        ],
        "mappings"=>[
            "default"=>[
                "properties"=>[
                    "userId"=> [
                        "type" => "integer"
                    ],
                    "content"=>[
                        "type" => "text",
                        "analyzer"=>"standard"
                    ],
                    "anonymous"=>[
                        "type"=>"boolean"
                    ],
                    "typeId"=>[
                        "type"=>"integer"
                    ],
                    "status"=>[
                        "type"=>"boolean"
                    ],
                    "parise"=>[
                        "type"=>"integer"
                    ],
                    "create_time"=>[
                        "type"=> "date",
                        "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                    ],
                    "update_time"=>[
                        "type"=> "date",
                        "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                    ]
                ]
            ]
        ]
    ]
];
$response = $client->indices()->create($params);
echo "创建评论索引成功:".$response."\r\n";

// 创建资讯索引
$params = [
    'index' => 'info',
    'body'=>[
        'settings'=>[
            'number_of_shards'=>2,
            'number_of_replicas'=>0
        ],
        "mappings"=>[
            "default"=>[
                "properties"=>[
                    "title"=>[
                        "type" => "text",
                        "analyzer"=>"standard"
                    ],
                    "content"=>[
                        "type" => "text",
                        "analyzer"=>"standard"
                    ],
                    "typeId"=>[
                        "type"=>"integer"
                    ],
                    "status"=>[
                        "type"=>"boolean"
                    ],
                    "create_time"=>[
                        "type"=> "date",
                        "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                    ],
                    "update_time"=>[
                        "type"=> "date",
                        "format"=>"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                    ]
                ]
            ]
        ]
    ]
];
$response = $client->indices()->create($params);
echo "创建资讯索引成功:".$response."\r\n";