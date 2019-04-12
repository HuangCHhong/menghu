<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 21:03
 */

namespace app\common\model;


use think\Model;
use app\admin\model\User;

class WeChat extends Model
{
    private static $login_url = "https://api.weixin.qq.com/sns/jscode2session";

    public static function getAppId($code){
        $param = [
            'appid'=>APPID,
            'secret'=>SECRET,
            'js_code'=>$code,
            'grant_type'=>'authorization_code',
        ];

        $response =  Access::deljson_arr(Curl::sendget(self::$login_url,$param));
        if($response['errcode'] == 0){
            //暂时将用户的登录态记录到session中
            Common::setSession(SESSION_OPENID,$response['openid']);
            Common::setSession(SESSION_KEY,$response['session_key']);
            Common::setSession(SESSION_UNIONID,$response['unionid']);

            return $response['openid'];
        }else {
            Access::Respond(0,array(),$response['errmsg']);
        }
    }

    public static function getUserInfo($rawData,$signature){
        // 获取存储的session_key
        $session_key = Common::getSession('session_key');
        if(empty($session_key)){
            Access::Respond(0,array(),'登录态获取失败，信息验证失败');
        }

        // 验证数据是否遭到串改
        $signature2 = sha1($rawData,$session_key);
        if($signature != $signature2){
            Access::Respond(0,array(),'数据验证失败，可能遭到串改');
        }

        // 进行数据存储
        $data = array(
            'name'=>$rawData['nickName'],
            'openid'=>$rawData['openId'],
            'city'=>$rawData['city'],
            'avatarUrl'=>$rawData['avatarUrl'],
        );
        if(User::in($data)){
            return $data;
        }else{
            Access::Respond(0,array(),'数据格式错误，存储数据失败');
        }
    }
}