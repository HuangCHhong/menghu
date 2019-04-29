<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/11
 * Time: 9:17
 */
namespace app\admin\model;
use app\common\model\RedisCache;
use GatewayClient\Gateway as GatewaySrc;
use think\facade\Config;

class Gateway
{
   public static function bind($uid,$client_id){
       GatewaySrc::$registerAddress = Config::get("REGISTER_ADD");

    // client_id与uid绑定
       GatewaySrc::bindUid($client_id, $uid);
    // 加入某个群组（可调用多次加入多个群组）
    // GatewaySrc::joinGroup($client_id, $group_id);
   }

   public static function sendToUid($uid,$message){
       GatewaySrc::$registerAddress = Config::get("REGISTER_ADD");
        //判断用户是否存在，如果存在则推送，不存在则放在redis中
       if(GatewaySrc::isUidOnline($uid)){
           // 向任意uid的网站页面发送数据
           GatewaySrc::sendToUid($uid, $message);
       }else{
           //使用hash结构存储到对应的redis中
            RedisCache::getInstance()->hSet($uid,time(),$message);
       }
   }

   public static function sendToGroup($group,$message){
       GatewaySrc::$registerAddress = Config::get("REGISTER_ADD");
       // 向任意uid的网站页面发送数据
       GatewaySrc::sendToUid($group, $message);
   }

}