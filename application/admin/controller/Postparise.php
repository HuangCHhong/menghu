<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:39
 */

namespace app\admin\controller;


use app\admin\model\Gateway;
use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\postParise as postPariseModel;
use app\admin\model\Post as postModel;
use \Config;
class Postparise extends Controller
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
        Authority::getInstance()->permit(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);
        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("postId");
        Access::MustParamDetectOfRawData($mustParam,$param);

        // 判断是否已经点过赞
        $data = postPariseModel::read(array("userId"=>$userId,"postId"=>$param["postId"]));
        if(count($data) > 0){
            Access::Respond(0,array(),"已经点过赞");
        }

        // 保存DB
        postPariseModel::in(array("userId"=>$userId,"postId"=>$param["postId"]));
        postModel::addParise($param["postId"]);

        //点赞成功推送给评论者
        $userInfo = \app\admin\model\User::getByUserId($userId);
        $message = array(
            'content'=>"有用户对你的帖子点了个赞，快点查看吧！",
            'postId'=>$param["postId"],
            'nickName'=>$userInfo["nickName"],
            'avatarUrl'=> $userInfo["avatarUrl"],
            'create_time'=>time(),
            'type'=>'post_parise'
        );

        $post = postModel::getById($param["postId"]);
        \app\admin\model\User::addParise($post["userId"]);

        Gateway::sendToUid($post["userId"],Access::json_arr($message));

        Access::Respond(1,array(),"点赞成功");
    }

    // 取消点赞
    public function delParise(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permitAll(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("postId");
        Access::MustParamDetectOfRawData($mustParam,$param);

        // 判断是否已经点过赞
        $data = postPariseModel::read(array("userId"=>$userId,"postId"=>$param["postId"]));
        if(count($data) <= 0){
            Access::Respond(0,array(),"没有点过赞");
        }
        // 保存DB
        postPariseModel::del(array($data[0]["id"]));
        postModel::delParise($param["postId"]);

        $post = postModel::getById($param["postId"]);
        \app\admin\model\User::delParise($post["userId"]);

        Access::Respond(1,array(),"取消点赞成功");
    }
}