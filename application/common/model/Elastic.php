<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/12
 * Time: 9:17
 */

namespace app\common\model;

use Elasticsearch\ClientBuilder;
use think\facade\Config;

class Elastic
{
    private static $instance = null;
    public $clinet = null;

    public function __construct(){
        $this->clinet =  ClientBuilder::create()->setHosts(Config::get("ElASTIC_HOST"))->build();
    }

    //文档格式转换
    public static function convert(&$data){
        if(isset($data["anonymous"])){
            $data["anonymous"] = boolval($data["anonymous"]);
        }
        if(isset($data["status"])){
            $data["status"] = boolval($data["status"]);
        }
    }

    public static function getInstance () {
        if (is_null(Elastic::$instance)) {
            self::$instance = new Elastic ();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    // 添加文档
    public function addDoc($id,$index,$data){
        self::convert($data);
        $param = [
            "index"=>$index,
            "type"=>$index,
            'id'=>$id,
            "body"=>$data
        ];
        $this->clinet->index($param);
    }

    // 删除文档
    public function delDoc($id,$index){
        $param = [
            "index"=>$index,
            "id"=>$id,
        ];
        $this->clinet->delete($param);
    }

    // 更新文档
    public function updDoc($id,$index,$data){
        self::convert($data);
        $param = [
            "index"=>$index,
            'id'=>$id,
            "body"=>$data
        ];
        $this->clinet->index($param);
    }

    // 创建索引
    public function createIndex($index,$mapping){
         $params = [
            'index' =>$index,
            'body'=>[
                'settings'=>[
                    'number_of_shards'=>2,
                    'number_of_replicas'=>0
                ],
                "mappings"=>$mapping
            ]
        ];
        $this->clinet->indices()->create($params);
    }
}