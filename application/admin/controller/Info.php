<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 9:16
 */

namespace app\admin\controller;

use app\common\model\Authority;
use app\common\model\Elastic;
use think\Controller;
use app\common\model\Access;
use app\admin\model\Info as InfoModel;
class Info extends Controller
{
    //获取动态资讯
    public function get(){
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        // 可能传入的参数
        $param = ["id","typeId","firstTime","endTime"];
        $paramList = Access::OptionalParamOfList($param);
        // 从数据库中获取相对应的值
        $data = InfoModel::read($paramList);
        Access::Respond(1,$data,"获取资讯成功");
    }

    //批量新增资讯
    public function batchAdd(){
        // 权限设置
        Authority::getInstance()->permit(array(ADMIN))->check(null);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必传参数
        $param = ["typeId","title","content"];
        for ($i=0;$i<count($data);$i++){
            Access::MustParamDetectOfRawData($param,$data[$i]);
        }
        // 保存到DB
        $idList = InfoModel::in($data);
        // 获取资讯
        $infoList = InfoModel::read(array("idList"=>$idList));
        // 添加到ES中
        foreach ($infoList as $info){
            Elastic::getInstance()->addDoc($info["id"],"info",$info);
        }

        Access::Respond(1,array(),"添加成功");
    }

    // 删除资讯
    public function batchDel(){
        // 权限设置
        Authority::getInstance()->permit(array(ADMIN))->check(null);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必传参数
        $param = ["idList"];
        Access::MustParamDetectOfRawData($param,$data);
        // 保存到DB
        InfoModel::del($data["idList"]);
        Access::Respond(1,array(),"删除资讯成功");
    }

    // 更新资讯
    public function updateInfo(){
        // 权限设置
        Authority::getInstance()->permit(array(ADMIN))->check(null);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必传参数
        $param = ["id"];
        Access::MustParamDetectOfRawData($param,$data);
        // 保存到DB
        InfoModel::upd($data["id"],$data);
        Access::Respond(1,array(),"更新资讯成功");
    }
}