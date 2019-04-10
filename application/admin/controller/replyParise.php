<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:39
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Access;
use app\common\model\Authority;
use app\admin\model\replyParise as replyPariseModel;
use app\admin\model\reply as replyModel;
class replyParise extends Controller
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
        Authority::getInstance()->permit(array(ORDINARY))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        $replyId = Access::MustParamDetect("replyId");

        // 判断是否已经点过赞
        $data = replyPariseModel::read(array("userId"=>$userId,"replyId"=>$replyId));
        if(count($data) > 0){
            Access::Respond(0,array(),"已经点过赞");
        }

        // 保存DB
        replyPariseModel::in(array("userId"=>$userId,"replyId"=>$replyId));
        replyModel::addParise($replyId);
        Access::Respond(1,array(),"点赞成功");
    }

    // 取消点赞
    public function delParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(ORDINARY))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        $replyId = Access::MustParamDetect("replyId");

        // 判断是否已经点过赞
        $data = replyPariseModel::read(array("userId"=>$userId,"replyId"=>$replyId));
        if(count($data) <= 0){
            Access::Respond(0,array(),"没有点过赞");
        }
        // 保存DB
        replyPariseModel::del(array($data[0]["id"]));
        replyModel::delParise($replyId);
        Access::Respond(1,array(),"取消点赞成功");
    }
}