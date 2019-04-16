<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/16
 * Time: 15:29
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Elastic as ElasticModel;
class Elastic extends Controller
{
    public function createIndex(){
        $post_mapping = [
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
        ];

        ElasticModel::getInstance()->createIndex('post',$post_mapping);
    }
}