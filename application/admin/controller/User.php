<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 11:21
 */

namespace app\admin\controller;


use app\common\model\Access;
use think\Controller;
use app\common\model\WeChat;
use app\common\model\Authority;
use app\admin\model\User as UserModel;
class User extends Controller
{
    // 构建登录态,首次登陆只返回openId供前段去调取用户基本信息；再次登陆就直接返回全量基本数据
    public function login(){
        // 获取前端从小程序拿到的code
        $code = Access::MustParamDetect("code");
        // 设置对应的OpenId和session_key
        $openid = WeChat::getAppId($code);
        // 检测该用户是否曾经登录过
        $result = UserModel::read($openid);
        if(count($result) ==  1){
            Access::Respond(1,$result,"信息获取成功");
        }
        // 如果是新用户，则创建并存储到DB中
        $ok = UserModel::in(array("openid"=>$openid));
        if(!$ok) {
            Access::Respond(0, array(), "创建新用户失败");
        }
        Access::Respond(1,UserModel::read($openid),"用户创建成功，请拉取用户数据");
    }

    // 设置用户开放数据
    public function setUserInfo(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("id","openId","nickName","roleId","avatarUrl","city");
        Access::MustParamDetectOfRawData($mustParam,$data);

        $ok = UserModel::upd($data["id"],$data);
        if(!$ok){
            Access::Respond(0,array(),"设置用户基本信息失败");
        }
        Access::Respond(1,UserModel::read($data["openId"]),"设置基本信息成功");
    }

    // 编辑用户数据
    public function updUserInfo(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("id");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 权限检测
        Authority::getInstance()->permit(array(ORDINARY))->check($data["id"]);

        $ok = UserModel::upd($data["id"],$data);
        if(!$ok){
            Access::Respond(0,array(),"修改基本信息失败");
        }
        Access::Respond(1,UserModel::read($data["openId"]),"修改基本信息成功");
    }

    // GET：查看用户数据
    public function getUserInfo(){
        //必选参数
        $userId = Access::MustParamDetect("id");
        // 权限设置
        Authority::getInstance()->permit(array(ORDINARY,ADMIN))->check($userId);
        // 从数据库中获取相对应的值
        $data = UserModel::getByUserId($userId);
        Access::Respond(1,$data,"获取用户数据成功");
    }

    // POST：批量获取用户数据
    public function getUserList(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("idList");
        Access::MustParamDetectOfRawData($mustParam,$data);

        // 权限检测
        Authority::getInstance()->permit(array(ADMIN))->check(null);
        $data = UserModel::batchRead($data["idList"]);
        Access::Respond(1,$data,"批量获取用户数据成功");
    }
}