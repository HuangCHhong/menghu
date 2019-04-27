<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:35
 */

namespace app\admin\controller;


use app\admin\model\Gateway;
use app\common\model\Elastic;
use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\reply as replyModel;
use app\admin\model\User as UserModel;
use app\admin\model\Post as PostModel;
use app\admin\model\weight as weightModel;
use \Config;

class Reply extends Controller
{
    // 查看回复详情
    public function getReplyInfo(){
        $randName = ["凯","宫本武藏","苍老师","狄仁杰","路飞","魏锁辉"];
        $randUrl = [
            "https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=4037537948,2376389778&fm=26&gp=0.jpg",
            "https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1604109836,3532046058&fm=26&gp=0.jpg",
            "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1973317496,452181970&fm=26&gp=0.jpg",
        ];
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        // 必选参数
        Access::MustParamDetect('postId');
        // 可选参数
        $param = array('id','idList','postId','userId','firstTime','endTime');
        $paramList = Access::OptionalParamOfList($param);
        // 获取评论数据
        $data = replyModel::read($paramList);
        // 获取可信度
        foreach ($data as &$replyInfo){
            // 获取帖子信息
            $post = PostModel::getById($replyInfo["postId"]);
            // 获取回帖人详情
            $user = UserModel::getByUserId( $replyInfo["userId"]);
            // 获取话题相关系数
            $weight = weightModel::getById($post["typeId"],$user["roleId"]);
            $weightNum = 1;
            if(!empty($weight)){
                $weightNum = $weight["weight"];
            }
            // 权威值计算公式：weight*praiseCount
            $replyInfo["score"] = $weightNum * $user["praiseCount"];
            //获取用户信息
            if(!$replyInfo["anonymous"]){
                $replyInfo["nickName"] = $user["nickName"];
                $replyInfo["avatarUrl"] = $user["avatarUrl"];
            }else{
                $replyInfo["nickName"] = $randName[mt_rand(0,count($randName)-1)];
                $replyInfo["avatarUrl"] = $randUrl[mt_rand(0,count($randUrl)-1)];
            }
        }
        Access::Respond(1,$data,"获取帖子回复成功");
    }

    // 删除帖子回复
    public function delReply(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("idLIst");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 获取帖子所有者
        $userList = array();
        $replyIdList = array();
        $replyInfos = replyModel::read($data);
        foreach ($replyInfos as $replyInfo){
            array_push($userList,$replyInfo["userId"]);
            array_push($postIdList,$replyInfo["id"]);
        }
        $userList = array_unique($userList);
        $replyIdList = array_unique($replyIdList);
        // 权限验证
        if(count($userList) > 1){
            // 涉及到多个用户的帖子，则只有管理员才有权限操作
            Authority::getInstance()->permit(array(Config::get("ADMIN")))->check(null);
        }
        Authority::getInstance()->permit(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check($userList[0]);
        // 帖子删除
        $ok = replyModel::del($replyIdList);
        if(!$ok){
            Access::Respond(0,array(),"评论删除失败");
        }
        Access::Respond(1,array(),"评论删除成功");
    }

    public function addReply(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("content","postId");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 存储到DB
        $data["userId"] = $userId;
        $id = replyModel::in($data);
        // 存储到ES
        $reply = replyModel::getById($id);
        Elastic::getInstance()->addDoc($reply["id"],"reply",$reply);
        // 评论成功后发送给发帖人
        $post = PostModel::getById($data["postId"]);
        $message = array(
            'content'=>"收到一条评论，可立即查看。帖子内容:".$data["content"],
            'postId'=>$data["postId"],
            );
        Gateway::sendToUid($post["userId"],Access::json_arr($message));
        Access::Respond(1,array(),"评论成功");
    }
}