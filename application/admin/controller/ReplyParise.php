<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:39
 */

namespace app\admin\controller;


use app\admin\model\Gateway;
use think\Config;
use think\Controller;
use app\common\model\Access;
use app\common\model\Authority;
use app\admin\model\replyParise as replyPariseModel;
use app\admin\model\reply as replyModel;
class ReplyParise extends Controller
{
// 查看点赞人数
    public function getParise(){
        // 权限验证
        Authority::getInstance()->permitAll(true)->check(null);
        // 参数验证
        $replyId = Access::MustParamDetect("replyId");
        $data = replyPariseModel::read(array("replyId"=>$replyId));
        Access::Respond(1,$data,"点赞情况获取成功");
    }

    // 点赞
    public function saveParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(\think\facade\Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("replyId");
        Access::MustParamDetectOfRawData($mustParam,$param);

        // 判断是否已经点过赞
        $data = replyPariseModel::read(array("userId"=>$userId,"replyId"=>$param["replyId"]));
        if(count($data) > 0){
            Access::Respond(0,array(),"已经点过赞");
        }

        // 保存DB
        replyPariseModel::in(array("userId"=>$userId,"replyId"=>$param["replyId"]));
        replyModel::addParise($param["replyId"]);

        //点赞成功推送给评论者
        $reply = replyModel::getById($param["replyId"]);
        Gateway::sendToUid($reply["userId"],"有用户对你的评论点了个赞，快点查看吧！");

        Access::Respond(1,array(),"点赞成功");
    }

    // 取消点赞
    public function delParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(\think\facade\Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("replyId");
        Access::MustParamDetectOfRawData($mustParam,$param);


        // 判断是否已经点过赞
        $data = replyPariseModel::read(array("userId"=>$userId,"replyId"=>$param["replyId"]));
        if(count($data) <= 0){
            Access::Respond(0,array(),"没有点过赞");
        }
        // 保存DB
        replyPariseModel::del(array($data[0]["id"]));
        replyModel::delParise($param["replyId"]);
        Access::Respond(1,array(),"取消点赞成功");
    }
}