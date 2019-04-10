<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:39
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\postParise as postPariseModel;
use app\admin\model\post as postModel;
class postParise extends Controller
{
    // 查看点赞人数
    public function getParise(){
        // 权限验证
        Authority::getInstance()->permitAll(true)->check(null);
        // 参数验证
        $postId = Access::MustParamDetect("postId");
        $data = postPariseModel::read(array("postId"=>$postId));
        Access::Respond(1,$data,"点赞情况获取成功");
    }

    // 点赞
    public function saveParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(ORDINARY))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        $postId = Access::MustParamDetect("postId");

        // 判断是否已经点过赞
        $data = postPariseModel::read(array("userId"=>$userId,"postId"=>$postId));
        if(count($data) > 0){
            Access::Respond(0,array(),"已经点过赞");
        }

        // 保存DB
        postPariseModel::in(array("userId"=>$userId,"postId"=>$postId));
        postModel::addParise($postId);
        Access::Respond(1,array(),"点赞成功");
    }

    // 取消点赞
    public function delParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permitAll(array(ORDINARY))->check(null)->loadAccount($flag,$userId);

        // 参数验证
        $postId = Access::MustParamDetect("postId");

        // 判断是否已经点过赞
        $data = postPariseModel::read(array("userId"=>$userId,"postId"=>$postId));
        if(count($data) <= 0){
            Access::Respond(0,array(),"没有点过赞");
        }
        // 保存DB
        postPariseModel::del(array($data[0]["id"]));
        postModel::delParise($postId);
        Access::Respond(1,array(),"取消点赞成功");
    }
}