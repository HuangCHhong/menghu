<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:37
 */

namespace app\admin\controller;


use app\admin\model\Gateway;
use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\relationship as relationshipModel;
use \Config;

class Relationship extends Controller
{
    // 添加关注
    public function addAttention(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("userId");
        Access::MustParamDetectOfRawData($mustParam,$param);


        // 检测是否已经关注
        $data = relationshipModel::read(array("userIdList"=>array($userId),"attUserIdList"=>array($param["userId"])));
        if(count($data) > 0){
            Access::Respond(0,array(),"已关注");
        }
        $ok = relationshipModel::in(array("userId"=>$userId,"attUserId"=>$param["userId"]));
        if(!$ok){
            Access::Respond(0,array(),"关注失败");
        }

        // 关注成功推送给被关注者
        $userInfo = \app\admin\model\User::getByUserId($userId);
        $message = array(
            'content'=>"有用户关注了你，快点查看吧",
            'userId'=>$userId,
            'nickName'=>$userInfo["nickName"],
            'avatarUrl'=> $userInfo["avatarUrl"],
            'create_time'=>time(),
            'type'=>'relation'
        );
        Gateway::sendToUid($param["userId"],Access::json_arr($message));

        Access::Respond(1,array(),"关注成功");
    }

    // 取消关注
    public function delAttention(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        // 解析json
        $param = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("userId");
        Access::MustParamDetectOfRawData($mustParam,$param);

        // 检测是否已经关注
        $data = relationshipModel::read(array("userIdList"=>array($userId),"attUserIdList"=>array($param["userId"])));
        if(count($data) <= 0){
            Access::Respond(0,array(),"未关注");
        }
        $ok = relationshipModel::del($data[0]["id"]);
        if(!$ok){
            Access::Respond(0,array(),"取消关注失败");
        }
        Access::Respond(1,array(),"取消关注成功");
    }

    // 查看关注
    public function getAttention(){
        // 权限验证
        Authority::getInstance()->permitAll(true)->check(null);
        // 参数验证
        $userId = Access::MustParamDetect("userId");

        $data = relationshipModel::read(array("userIdList"=>array($userId)));
        Access::Respond(1,$data,"获取关注情况成功");
    }
}