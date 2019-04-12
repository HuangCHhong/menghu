<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:35
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\Post as PostModel;
class post extends Controller
{
    // 查看帖子信息
    public function getPostInfo(){
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        // 可选参数
        $param = array('id','idList','userId','typeId','firstTime','endTime');
        $paramList = Access::OptionalParamOfList($param);
        // 获取数据
        $data = PostModel::read($paramList);
        Access::Respond(1,$data,"获取帖子列表成功");
    }

    // 删除帖子
    public function batchDelPost(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("idLIst");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 获取帖子所有者
        $userList = array();
        $postIdList = array();
        $postInfos = PostModel::read($data);
        foreach ($postInfos as $postInfo){
            $userList = array_push($userList,$postInfo["userId"]);
            $postIdList = array_push($postIdList,$postInfo["id"]);
        }
        $userList = array_unique($userList);
        $postIdList = array_unique($postIdList);
        // 权限验证
        if(count($userList) > 1){
            // 涉及到多个用户的帖子，则只有管理员才有权限操作
            Authority::getInstance()->permit(array(ADMIN))->check(null);
        }
        Authority::getInstance()->permit(array(ADMIN,ORDINARY))->check($userList[0]);
        // 帖子删除
        $ok = PostModel::del($postIdList);
        if(!$ok){
            Access::Respond(0,array(),"帖子删除失败");
        }
        Access::Respond(1,array(),"帖子删除成功");
    }

    public function addPost(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(ORDINARY))->check(null)->loadAccount($flag,$userId);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("content,typeId");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 存储到DB
        $data["userId"] = $userId;
        $ok = PostModel::in($data);
        if(!$ok){
            Access::Respond(0,array(),"帖子发布失败");
        }
        Access::Respond(1,array(),"帖子发布成功");
    }

    public function updPost(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("id");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 权限校准
        $data = PostModel::getById($data["id"]);
        Authority::getInstance()->permit(array(ORDINARY))->check($data["userId"]);
        // 更新帖子
        PostModel::upd($data["id"],$data);
    }
}