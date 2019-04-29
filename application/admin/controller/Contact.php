<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/11
 * Time: 11:12
 */

namespace app\admin\controller;


use app\common\model\Access;
use app\common\model\Authority;
use app\admin\model\Gateway;
use app\common\model\RedisCache;
use \Config;

class Contact
{
    // 小程序上线推送client_id
    public function setClient(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);

        //参数验证
        $client_id = Access::MustParamDetect('client_id');

        //client_id与uid绑定
        Gateway::bind($userId,$client_id);
        //判断是否有消息需要推送
        $result = RedisCache::getInstance()->hGetall($userId);
        foreach ($result as $info){
            Gateway::sendToUid($userId,$info);
        }
        //推送完将redis删除
        RedisCache::getInstance()->del($userId);
        Access::Respond(1,array(),"clientId与userId绑定成功");
    }
}