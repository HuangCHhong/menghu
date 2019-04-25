<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 21:03
 */

namespace app\common\model;


use \Config;
use think\Model;
use app\admin\model\User;
use app\common\model\Common;
class WeChat extends Model
{
    private static $login_url = "https://api.weixin.qq.com/sns/jscode2session";

    public static function getAppId($code){
        $param = [
            'appid'=>Config::get("APPID"),
            'secret'=>Config::get("SECRET"),
            'js_code'=>$code,
            'grant_type'=>'authorization_code',
        ];

        $response =  Access::deljson_arr(Curl::sendget(self::$login_url,$param));
        if(isset($response['openid']) && isset($response["session_key"])){
            //暂时将用户的登录态记录到session中
            Common::setSession(Config::get("SESSION_OPENID"),$response['openid'],false);
            Common::setSession(Config::get("SESSION_KEY"),$response['session_key'],false);
//            Common::setSession(Config::get("SESSION_UNIONID"),$response['unionid']);

            return $response['openid'];
        }else {
            Access::Respond(0,array(),"去微信服务器请求授权失败");
        }
    }

    public static function setUserInfo($rawData,$signature){
        // 获取存储的session_key
        $session_key = Common::getSession('session_key');
        if(empty($session_key)){
            Access::Respond(0,array(),'登录态获取失败，信息验证失败');
        }

        // 验证数据是否遭到串改
//        $signature2 = sha1($rawData,$session_key);
//        if($signature != $signature2){
//            Access::Respond(0,array(),'数据验证失败，可能遭到串改');
//        }

        // 进行数据存储
        $data = array(
            'nickName'=>$rawData['nickName'],
//            'openId'=>$rawData['roleId'],
            'city'=>$rawData['city'],
            'avatarUrl'=>$rawData['avatarUrl'],
            'roleId'=>$rawData['roleId'],
        );
        // 存储session
        Common::setSession(Config::get("SESSION_FLAG"),$rawData["roleId"]);

        if(User::upd($rawData["id"],$data)){
            return true;
        }else{
            Access::Respond(0,array(),'数据格式错误，存储数据失败');
        }
    }
}