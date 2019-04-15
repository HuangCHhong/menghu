<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/11
 * Time: 9:17
 */

namespace app\admin\model;
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

    // 向任意uid的网站页面发送数据
       GatewaySrc::sendToUid($uid, $message);
   }

   public static function sendToGroup($group,$message){
       GatewaySrc::$registerAddress = Config::get("REGISTER_ADD");

       // 向任意uid的网站页面发送数据
       GatewaySrc::sendToUid($group, $message);
   }

}